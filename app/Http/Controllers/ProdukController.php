<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategoris');

        // Pencarian berdasarkan nama produk
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama', 'like', "%{$searchTerm}%");
        }

        $produks = $query->orderBy('nama')->paginate(10);
        
        // Mempertahankan parameter pencarian pada pagination links
        if ($request->has('search')) {
            $produks->appends(['search' => $request->search]);
        }
        
        return view('produks.index', compact('produks'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('produks.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|max:2048',
            'kategori_ids' => 'required|array',
            'kategori_ids.*' => 'exists:kategoris,id',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
            ];

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('produks', $filename,'public');
                $data['gambar'] = $filename;
            }

            $produk = Produk::create($data);

            // Attach categories
            $produk->kategoris()->attach($request->kategori_ids);

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::all();
        $selectedKategoris = $produk->kategoris->pluck('id')->toArray();

        return view('produks.edit', compact('produk', 'kategoris', 'selectedKategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|max:2048',
            'kategori_ids' => 'required|array',
            'kategori_ids.*' => 'exists:kategoris,id',
        ]);

        DB::beginTransaction();

        try {
            $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
            ];

            if ($request->hasFile('gambar')) {
                // Delete old image
                if ($produk->gambar) {
                    Storage::delete('public/produks/' . $produk->gambar);
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('produks', $filename,'public');
                $data['gambar'] = $filename;
            }

            $produk->update($data);

            // Sync categories
            $produk->kategoris()->sync($request->kategori_ids);

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Produk $produk)
    {
        DB::beginTransaction();

        try {
            // Delete image if exists
            if ($produk->gambar) {
                Storage::delete('public/produks/' . $produk->gambar);
            }

            // Detach all categories
            $produk->kategoris()->detach();

            // Delete product
            $produk->delete();

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
