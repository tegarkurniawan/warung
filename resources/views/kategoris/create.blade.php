@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
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
                <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
