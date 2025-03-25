@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga') }}" required>
                </div>
                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kategori_ids" class="form-label">Kategori (Multiple)</label>
                <select class="form-select @error('kategori_ids') is-invalid @enderror" id="kategori_ids" name="kategori_ids[]" multiple required>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ in_array($kategori->id, old('kategori_ids', [])) ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
