<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        
        // Filter produk berdasarkan kategori jika ada
        if ($request->has('kategori')) {
            $produks = Produk::whereHas('kategoris', function($query) use ($request) {
                $query->where('kategoris.id', $request->kategori);
            })->with('kategoris')->get();
        } else {
            $produks = Produk::with('kategoris')->get();
        }
        
        return view('pos.index', compact('kategoris', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:255',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:produks,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Hitung total harga
            $totalHarga = 0;
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id']);
                
                // Cek stok
                if ($produk->stok < $item['jumlah']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok produk {$produk->nama} tidak mencukupi! Tersedia: {$produk->stok}"
                    ], 400);
                }
                
                $totalHarga += $produk->harga * $item['jumlah'];
            }

            // Buat pembelian
            $pembelian = Pembelian::create([
                'nama_pembeli' => $request->nama_pembeli,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->status_pembayaran,
                'created_by' => Auth::user()->email
            ]);

            // Tambahkan detail pembelian dan kurangi stok
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id']);
                
                // Kurangi stok
                $produk->stok -= $item['jumlah'];
                $produk->save();
                
                // Tambahkan detail pembelian
                $pembelian->detail()->create([
                    'produk_id' => $item['id'],
                    'harga' => $produk->harga,
                    'jumlah' => $item['jumlah']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'pembelian_id' => $pembelian->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function prosesPembayaran(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:255',
            'produk' => 'required|array',
            'jumlah' => 'required|array',
            'total_harga' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris',
            'status_pembayaran' => 'required|in:lunas,belum_lunas'
        ]);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::create([
                'nama_pembeli' => $request->nama_pembeli,
                'total_harga' => $request->total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => $request->status_pembayaran,
                'created_by' => Auth::user()->email
            ]);

            foreach ($request->produk as $index => $produk_id) {
                $produk = Produk::findOrFail($produk_id);
                
                // Kurangi stok
                if ($produk->stok >= $request->jumlah[$index]) {
                    $produk->stok -= $request->jumlah[$index];
                    $produk->save();
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok produk {$produk->nama} tidak mencukupi! Tersedia: {$produk->stok}"
                    ], 400);
                }
                
                $pembelian->detail()->create([
                    'produk_id' => $produk_id,
                    'harga' => $produk->harga,
                    'jumlah' => $request->jumlah[$index]
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'pembelian_id' => $pembelian->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cetakStruk($pembelianId)
    {
        $pembelian = Pembelian::with('detail.produk')->findOrFail($pembelianId);
        return view('pos.struck', compact('pembelian'));
    }
}
