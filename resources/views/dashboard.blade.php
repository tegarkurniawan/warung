@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xl">
    <div class="row row-cards row-deck">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Produk</div>
                    </div>
                    <div class="h1 mb-3">{{ $totalProduk }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Kategori</div>
                    </div>
                    <div class="h1 mb-3">{{ $totalKategori }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total User</div>
                    </div>
                    <div class="h1 mb-3">{{ $totalUser }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Pembelian</div>
                    </div>
                    <div class="h1 mb-3">{{ $totalPembelian }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row row-cards row-deck mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik Penjualan</h3>
                </div>
                <div class="card-body">
                    <canvas id="chart-penjualan" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Produk dengan stok hampir habis -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Produk dengan Stok Kurang dari 10</h3>
                    @if(count($lowStockProducts) > 0)
                    <a href="{{ route('produk.index') }}" class="btn btn-primary btn-sm">Lihat Semua Produk</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(count($lowStockProducts) > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $produk)
                                <tr>
                                    <td>
                                        <img src="{{ asset('storage/produks/' . $produk->gambar) }}" 
                                             alt="{{ $produk->nama }}"
                                             class="avatar" style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td>{{ $produk->nama }}</td>
                                    <td>
                                        @foreach($produk->kategoris as $kategori)
                                            <span class="badge bg-blue-lt me-1">{{ $kategori->nama }}</span>
                                        @endforeach
                                    </td>
                                    <td>Rp. {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $produk->stok == 0 ? 'bg-danger' : 'bg-warning' }}">
                                            {{ $produk->stok }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($produk->stok == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @else
                                            <span class="badge bg-warning">Low Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('produk.edit', $produk->id) }}" class="btn btn-sm btn-primary">
                                            Update Stok
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Semua produk memiliki stok yang cukup.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk grafik
    const dataPenjualan = {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Total Penjualan',
            data: {!! json_encode($data) !!},
            borderColor: '#206bc4',
            backgroundColor: 'rgba(32, 107, 196, 0.1)',
            borderWidth: 2,
            fill: true
        }]
    };

    // Konfigurasi grafik
    const configPenjualan = {
        type: 'line',
        data: dataPenjualan,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    };

    // Inisialisasi grafik
    const ctxPenjualan = document.getElementById('chart-penjualan');
    if (ctxPenjualan) {
        new Chart(ctxPenjualan, configPenjualan);
    }
});
</script>
@endpush
@endsection
