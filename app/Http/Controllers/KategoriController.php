<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        // Pencarian berdasarkan nama kategori
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama', 'like', "%{$searchTerm}%");
        }

        $kategoris = $query->orderBy('nama')->paginate(10);
        
        // Mempertahankan parameter pencarian pada pagination links
        if ($request->has('search')) {
            $kategoris->appends(['search' => $request->search]);
        }
        
        return view('kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategoris.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
        ];

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategoris', $filename,'public');
            $data['gambar'] = $filename;
        }

        Kategori::create($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
        ]);

        $data = [
            'nama' => $request->nama,
        ];

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($kategori->gambar) {
                Storage::delete('public/kategoris/' . $kategori->gambar);
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('kategoris', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $kategori->update($data);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Kategori $kategori)
    {
        // Delete image if exists
        if ($kategori->gambar) {
            Storage::delete('public/kategoris/' . $kategori->gambar);
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}
