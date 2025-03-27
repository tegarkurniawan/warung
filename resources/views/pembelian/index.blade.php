@extends('layouts.app')

@section('title', 'Daftar Pembelian')

@section('content')
<div class="container-xl py-4">
    <div class="d-flex justify-content-between mb-3">
        <h2 class="mb-0">Daftar Pembelian</h2>
        
        <form action="{{ route('pembelian.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-icon">
                <input type="text" name="search" class="form-control" placeholder="Cari nama pembeli..." value="{{ request('search') }}">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                </span>
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if(request('search') || request('start_date') || request('end_date'))
                <a href="{{ route('pembelian.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Filter Tanggal</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('pembelian.index') }}" method="GET" class="row g-3">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            Filter
                        </button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ request('search') ? route('pembelian.index', ['search' => request('search')]) : route('pembelian.index') }}" class="btn btn-outline-secondary">
                                Reset Tanggal
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pembelian</h3>
        </div>
        <div class="card-body">
            @if($pembelian->isEmpty())
                <div class="alert alert-info">
                    @if(request('search') || request('start_date') || request('end_date'))
                        Tidak ada pembelian yang sesuai dengan filter.
                    @else
                        Belum ada data pembelian.
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pembeli</th>
                                <th>Produk Dibeli</th>
                                <th>Total Harga</th>
                                <th>Metode Pembayaran</th>
                                <th>Status Pembayaran</th>
                                <th>Tanggal Pembelian</th>
                                @if(auth()->user()->role === 'admin')
                                    <th>Dibuat Oleh</th>
                                @endif
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembelian as $index => $item)
                            <tr>
                                <td>{{ ($pembelian->currentPage() - 1) * $pembelian->perPage() + $index + 1 }}</td>
                                <td>{{ $item->nama_pembeli }}</td>
                                <td>{{ $item->detail->pluck('produk.nama')->implode(', ') }}</td>
                                <td>Rp. {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                <td>{{ $item->metode_pembayaran }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status_pembayaran === 'lunas' ? 'success' : 'warning' }}">
                                        {{ $item->status_pembayaran }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                @if(auth()->user()->role === 'admin')
                                    <td>{{ $item->created_by }}</td>
                                @endif
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('pembelian.detail', $item->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                        </a>
                                        <button class="btn btn-sm btn-success cetak-struk" title="Cetak Struk" data-id="{{ $item->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            
            <div class="mt-4 d-flex justify-content-center">
                {{ $pembelian->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Cetak Struk -->
<div class="modal fade" id="struk-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Struk Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="struk-content"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="cetakStruk()">Cetak Struk</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handler untuk tombol Cetak Struk
    $(document).on('click', '.cetak-struk', function() {
        let pembelianId = $(this).data('id');
        tampilkanStruk(pembelianId);
    });

    // Fungsi untuk menampilkan struk
    function tampilkanStruk(pembelianId) {
        $.get(`/pembelian/cetak-struk/${pembelianId}`, function(html) {
            $('#struk-content').html(html);
            $('#struk-modal').modal('show');
        }).fail(function() {
            alert('Gagal mengambil data struk pembelian');
        });
    }
});

// Fungsi untuk mencetak struk
function cetakStruk() {
    let printContents = document.getElementById('struk-content').innerHTML;
    let originalContents = document.body.innerHTML;

    let printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.open();
    printWindow.document.write(`
        <html>
        <head>
            <title>Cetak Struk</title>
            <style>
                body { font-family: Arial, sans-serif; }
                table { width: 100%; border-collapse: collapse; }
                td, th { border: 1px solid #000; padding: 8px; text-align: left; }
            </style>
        </head>
        <body>
            ${printContents}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}
</script>
@endpush
