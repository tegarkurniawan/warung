<?php
namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Pembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $produks = Produk::with('kategoris')->get();
        return view('pos.index', compact('kategoris', 'produks'));
    }

    public function getProdukByKategori($kategori_id)
    {
        $produks = Produk::whereHas('kategoris', function($query) use ($kategori_id) {
            $query->where('kategoris.id', $kategori_id);
        })->get();

        return response()->json($produks);
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
                'status_pembayaran' => $request->status_pembayaran
            ]);

            foreach ($request->produk as $index => $produk_id) {
                $produk = Produk::findOrFail($produk_id);
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
