@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Pembelian</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pembeli</th>
                <th>Total Harga</th>
                <th>Tanggal Pembelian</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pembelian as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_pembeli }}</td>
                <td>Rp. {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                <td>
                    <a href="{{ route('pembelian.detail', $item->id) }}" class="btn btn-info btn-sm">Detail</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
