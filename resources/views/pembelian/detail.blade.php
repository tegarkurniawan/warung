@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Pembelian</h1>
    <div class="card">
        <div class="card-header">
            <h3>Informasi Pembelian</h3>
        </div>
        <div class="card-body">
            <p><strong>Nama Pembeli:</strong> {{ $pembelian->nama_pembeli }}</p>
            <p><strong>Total Harga:</strong> Rp. {{ number_format($pembelian->total_harga, 0, ',', '.') }}</p>
            <p><strong>Metode Pembayaran:</strong> {{ $pembelian->metode_pembayaran }}</p>
            <p><strong>Status Pembayaran:</strong> {{ $pembelian->status_pembayaran }}</p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3>Produk yang Dibeli</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembelian->detail as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->produk->nama }}</td>
                        <td>Rp. {{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>Rp. {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
