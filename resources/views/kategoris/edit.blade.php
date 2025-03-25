@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $kategori->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar (Opsional)</label>
                @if($kategori->gambar)
                    <div class="mb-2">
                        <img src="{{ asset('storage/kategoris/' . $kategori->gambar) }}" alt="{{ $kategori->nama }}" height="100">
                    </div>
                @endif
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                @error('gambar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
