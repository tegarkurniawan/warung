@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="mb-3">
    <a href="{{ route('produk.create') }}" class="btn btn-primary">Tambah Produk</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gambar</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produks as $index => $produk)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $produk->nama }}</td>
                    <td>
                        @if($produk->gambar)
                            <img src="{{ asset('storage/produks/' . $produk->gambar) }}" alt="{{ $produk->nama }}" height="50">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td>{{ number_format($produk->harga, 0, ',', '.') }}</td>
                    <td>
                        @foreach($produk->kategoris as $kategori)
                            <span class="badge bg-info">{{ $kategori->nama }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
