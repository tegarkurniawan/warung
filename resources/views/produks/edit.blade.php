@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Edit Produk</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data" class="max-w-lg">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
            <input type="text" name="nama" id="nama"
                   value="{{ old('nama', $produk->nama) }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('nama') border-red-500 @enderror">
            @error('nama')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="harga" class="block text-gray-700 text-sm font-bold mb-2">Harga</label>
            <input type="number" name="harga" id="harga"
                   value="{{ old('harga', $produk->harga) }}"
                   min="0" step="0.01"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('harga') border-red-500 @enderror">
            @error('harga')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="gambar" class="block text-gray-700 text-sm font-bold mb-2">Gambar Produk</label>
            <input type="file" name="gambar" id="gambar"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('gambar') border-red-500 @enderror">
            @error('gambar')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror

            @if($produk->gambar)
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Gambar Saat Ini:</p>
                    <img src="{{ asset('storage/produks/' . $produk->gambar) }}"
                         alt="{{ $produk->nama }}"
                         class="mt-2 h-32 w-auto object-cover">
                </div>
            @endif
        </div>

        <div class="mb-3">
            <label for="kategori_ids" class="block text-gray-700 text-sm font-bold mb-2">Kategori (Multiple)</label>
            <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                   @error('kategori_ids') border-red-500 @enderror"
                    id="kategori_ids"
                    name="kategori_ids[]"
                    multiple
                    required>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}"
                            {{ in_array($kategori->id, old('kategori_ids', $selectedKategoris)) ? 'selected' : '' }}>
                        {{ $kategori->nama }}
                    </option>
                @endforeach
            </select>
            @error('kategori_ids')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Produk
            </button>
            <a href="{{ route('produk.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection
