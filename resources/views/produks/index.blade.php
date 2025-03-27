@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('produk.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
            Tambah Produk
        </a>
        
        <form action="{{ route('produk.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-icon">
                <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                </span>
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if(request('search'))
                <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Produk</h3>
        </div>
        <div class="card-body">
            @if($produks->isEmpty())
                <div class="alert alert-info">
                    @if(request('search'))
                        Tidak ada produk dengan kata kunci "{{ request('search') }}".
                    @else
                        Belum ada produk.
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Gambar</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produks as $index => $produk)
                            <tr>
                                <td>{{ ($produks->currentPage() - 1) * $produks->perPage() + $index + 1 }}</td>
                                <td>{{ $produk->nama }}</td>
                                <td>
                                    @if($produk->gambar)
                                        <img src="{{ asset('storage/produks/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="img-thumbnail" style="max-width: 50px; height: auto;">
                                    @else
                                        <span class="text-muted">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                <td>{{ number_format($produk->stok, 0, ',', '.') }}</td>
                                <td>
                                    @foreach($produk->kategoris as $kategori)
                                        <span class="badge bg-info">{{ $kategori->nama }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-sm btn-warning">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        </a>
                                        @if(Auth::user()->role === 'admin')
                                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $produks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
