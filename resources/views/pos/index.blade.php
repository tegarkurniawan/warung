@extends('layouts.app')

@section('content')
<style>
    .produk-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .produk-card .card-img-top {
        height: 150px;
        object-fit: cover;
    }
    .produk-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .produk-card .card-title {
        height: 40px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .produk-card .card-footer {
        margin-top: auto;
        background-color: transparent;
        border-top: none;
        padding-top: 0;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <!-- Kategori dan Produk -->
        <div class="col-md-8">
            <div class="row">
                <!-- Kategori -->
                <div class="col-md-3">
                    <div class="mb-3">
                        <div class="input-icon">
                            <input type="text" id="search-produk" class="form-control" placeholder="Cari produk...">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            </span>
                        </div>
                    </div>
                    <div class="list-group" id="kategori-list">
                        <a href="#" class="list-group-item list-group-item-action kategori-item active d-flex align-items-center"
                           data-kategori-id="all">
                            <div class="me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                            </div>
                            Semua Kategori
                        </a>
                        @foreach($kategoris as $kategori)
                        <a href="#" class="list-group-item list-group-item-action kategori-item d-flex align-items-center"
                           data-kategori-id="{{ $kategori->id }}">
                            <div class="me-2">
                                <img src="{{ asset('storage/kategoris/' . $kategori->gambar) }}" 
                                     alt="{{ $kategori->nama }}" 
                                     class="img-thumbnail"
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            </div>
                            {{ $kategori->nama }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Produk -->
                <div class="col-md-9">
                    <div class="row" id="produk-list">
                        @foreach($produks as $produk)
                        <div class="col-md-3 mb-3 produk-item"
                             data-kategori-ids="{{ $produk->kategoris->pluck('id')->implode(',') }}"
                             data-produk-nama="{{ strtolower($produk->nama) }}"
                             data-produk-kode="{{ $produk->kode }}"
                             data-page="{{ ceil(($loop->index + 1) / 12) }}">
                            <div class="card produk-card">
                                <img src="{{ asset('storage/produks/' . $produk->gambar) }}" class="card-img-top" alt="{{ $produk->nama }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $produk->nama }}</h5>
                                    <p class="card-text">Rp. {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                    <p class="card-text"><small class="text-muted">Stok: {{ $produk->stok }}</small></p>
                                    <div class="card-footer">
                                        <button class="btn btn-primary w-100 tambah-produk {{ $produk->stok <= 0 ? 'disabled' : '' }}"
                                                data-produk-id="{{ $produk->id }}"
                                                data-produk-nama="{{ $produk->nama }}"
                                                data-produk-harga="{{ $produk->harga }}"
                                                data-produk-stok="{{ $produk->stok }}"
                                                {{ $produk->stok <= 0 ? 'disabled' : '' }}>
                                            Tambah
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div id="no-products-message" class="alert alert-info d-none">
                        Tidak ada produk yang ditemukan.
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3" id="pagination-container">
                        <nav>
                            <ul class="pagination" id="pagination">
                                <!-- Pagination items will be generated by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keranjang Belanja -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Keranjang Belanja</div>
                <div class="card-body">
                    <form id="form-pembayaran">
                        <div class="mb-3">
                            <label>Nama Pembeli</label>
                            <input type="text" class="form-control" name="nama_pembeli" required>
                        </div>

                        <table class="table" id="keranjang-list">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="keranjang-body"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td id="total-harga">0</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="mb-3">
                            <label>Metode Pembayaran</label>
                            <select class="form-control" name="metode_pembayaran" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Status Pembayaran</label>
                            <select class="form-control" name="status_pembayaran" required>
                                <option value="lunas">Lunas</option>
                                <option value="belum_lunas">Belum Lunas</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">Bayar</button>
                    </form>
                </div>
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
    let keranjang = [];
    let totalHarga = 0;
    // Untuk menyimpan informasi stok produk
    let produkStok = {};
    
    // Pagination settings
    let itemsPerPage = 12;
    let currentPage = 1;
    let totalVisibleItems = 0;
    let totalPages = 0;
    
    // Initialize pagination
    function initPagination() {
        // Count visible items (considering filtering)
        totalVisibleItems = $('.produk-item:visible').length;
        totalPages = Math.ceil(totalVisibleItems / itemsPerPage);
        
        // Generate pagination links
        generatePaginationLinks();
        
        // Show only items for current page
        showCurrentPageItems();
    }
    
    // Generate pagination links
    function generatePaginationLinks() {
        let $pagination = $('#pagination');
        $pagination.empty();
        
        // Don't show pagination if we have only one page or no products
        if (totalPages <= 1) {
            $('#pagination-container').hide();
            return;
        }
        
        $('#pagination-container').show();
        
        // Previous button
        $pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
                    Prev
                </a>
            </li>
        `);
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            $pagination.append(`
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }
        
        // Next button
        $pagination.append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                </a>
            </li>
        `);
    }
    
    // Show items for current page
    function showCurrentPageItems() {
        let $visibleItems = $('.produk-item:visible');
        
        // Hide all visible items first
        $visibleItems.hide();
        
        // Show only items for current page
        let startIdx = (currentPage - 1) * itemsPerPage;
        let endIdx = Math.min(startIdx + itemsPerPage, totalVisibleItems);
        
        $visibleItems.slice(startIdx, endIdx).show();
    }
    
    // Pagination click handler
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        
        // Don't do anything if the link is disabled
        if ($(this).parent().hasClass('disabled')) {
            return;
        }
        
        // Update current page
        currentPage = parseInt($(this).data('page'));
        
        // Update pagination and show proper items
        generatePaginationLinks();
        showCurrentPageItems();
    });

    // Enhanced search functionality
    $('#search-produk').on('keyup', function() {
        let keyword = $(this).val().toLowerCase().trim();
        let foundProducts = 0;
        
        // Reset current page when searching
        currentPage = 1;
        
        $('.produk-item').each(function() {
            let produkNama = $(this).data('produk-nama');
            let produkKode = $(this).data('produk-kode') ? $(this).data('produk-kode').toString().toLowerCase() : '';
            
            if (produkNama.includes(keyword) || produkKode.includes(keyword) || keyword === '') {
                $(this).show();
                foundProducts++;
            } else {
                $(this).hide();
            }
        });
        
        // Tampilkan pesan jika tidak ada produk yang ditemukan
        if (foundProducts === 0) {
            $('#no-products-message').removeClass('d-none');
            $('#pagination-container').hide();
        } else {
            $('#no-products-message').addClass('d-none');
            // Update pagination
            initPagination();
        }
        
        // Reset kategori aktif jika pencarian dilakukan
        if (keyword !== '') {
            $('.kategori-item').removeClass('active');
            $('.kategori-item[data-kategori-id="all"]').addClass('active');
        }
    });

    // Filter produk berdasarkan kategori
    $('.kategori-item').on('click', function() {
        $('.kategori-item').removeClass('active');
        $(this).addClass('active');

        let kategoriId = $(this).data('kategori-id');
        
        // Reset pencarian dan current page saat memilih kategori
        $('#search-produk').val('');
        $('#no-products-message').addClass('d-none');
        currentPage = 1;

        if (kategoriId === "all") {
            $('.produk-item').show();
        } else {
            $('.produk-item').hide();
            $(`.produk-item[data-kategori-ids*="${kategoriId}"]`).show();
        }
        
        // Update pagination after filtering
        initPagination();
    });

    // Initialize pagination on page load
    initPagination();

    // Tambah produk ke keranjang
    $(document).on('click', '.tambah-produk', function() {
        let produkId = $(this).data('produk-id');
        let produkNama = $(this).data('produk-nama');
        let produkHarga = $(this).data('produk-harga');
        let produkStokItem = $(this).data('produk-stok');
        
        // Simpan informasi stok
        produkStok[produkId] = produkStokItem;

        let existingItem = keranjang.find(item => item.produk_id === produkId);
        if (existingItem) {
            // Cek stok sebelum menambah
            if (existingItem.jumlah < produkStok[produkId]) {
                existingItem.jumlah++;
            } else {
                alert(`Stok produk ${produkNama} tidak mencukupi!`);
            }
        } else {
            keranjang.push({
                produk_id: produkId,
                nama: produkNama,
                harga: produkHarga,
                jumlah: 1
            });
        }

        updateKeranjang();
    });

    $(document).on('click', '.btn-plus', function() {
        let produkId = $(this).data('produk-id');
        let item = keranjang.find(item => item.produk_id === produkId);
        if (item) {
            // Cek stok sebelum menambah
            if (item.jumlah < produkStok[produkId]) {
                item.jumlah++;
                updateKeranjang();
            } else {
                alert(`Stok produk ${item.nama} tidak mencukupi!`);
            }
        }
    });

    // Kurangi jumlah produk di keranjang
    $(document).on('click', '.btn-minus', function() {
        let produkId = $(this).data('produk-id');
        let item = keranjang.find(item => item.produk_id === produkId);
        if (item && item.jumlah > 1) {
            item.jumlah--;
        } else {
            keranjang = keranjang.filter(item => item.produk_id !== produkId);
        }
        updateKeranjang();
    });

    $(document).on('input', '.input-jumlah', function() {
        let produkId = $(this).data('produk-id');
        let item = keranjang.find(item => item.produk_id === produkId);
        let jumlahBaru = parseInt($(this).val());

        if (item) {
            // Cek stok dengan jumlah baru
            if (jumlahBaru <= produkStok[produkId]) {
                item.jumlah = jumlahBaru > 0 ? jumlahBaru : 1;
            } else {
                alert(`Stok produk ${item.nama} hanya tersedia ${produkStok[produkId]}!`);
                $(this).val(produkStok[produkId]);
                item.jumlah = produkStok[produkId];
            }
            updateKeranjang();
        }
    });

    // Hapus produk dari keranjang
    $(document).on('click', '.hapus-produk', function() {
        let produkId = $(this).data('produk-id');
        keranjang = keranjang.filter(item => item.produk_id !== produkId);
        updateKeranjang();
    });

    // Update keranjang
    function updateKeranjang() {
        let keranjangBody = $('#keranjang-body');
        keranjangBody.empty();
        totalHarga = 0;

        keranjang.forEach(item => {
            let subtotal = item.harga * item.jumlah;
            totalHarga += subtotal;
            let stokTersedia = produkStok[item.produk_id] || 0;
            let disablePlus = item.jumlah >= stokTersedia ? 'disabled' : '';

            keranjangBody.append(`
                <tr>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary btn-minus" data-produk-id="${item.produk_id}">-</button>
                        <input type="number" class="form-control d-inline input-jumlah" style="width: 50px; text-align: center;"
                            value="${item.jumlah}" min="1" max="${stokTersedia}" data-produk-id="${item.produk_id}">
                        <button class="btn btn-sm btn-outline-secondary btn-plus" data-produk-id="${item.produk_id}" ${disablePlus}>+</button>
                    </td>
                    <td>Rp. ${subtotal.toLocaleString('id-ID')}</td>
                    <td>
                        <button class="btn btn-sm btn-danger hapus-produk" data-produk-id="${item.produk_id}">
                            Hapus
                        </button>
                    </td>
                </tr>
            `);
        });

        $('#total-harga').text("Rp. " + totalHarga.toLocaleString('id-ID'));
    }

    // Proses pembayaran
    $('#form-pembayaran').on('submit', function(e) {
        e.preventDefault();

        if (keranjang.length === 0) {
            alert('Keranjang belanja masih kosong');
            return;
        }

        let produkIds = keranjang.map(item => item.produk_id);
        let jumlahProduk = keranjang.map(item => item.jumlah);

        let formData = {
            _token: $('meta[name="csrf-token"]').attr('content'), // Ambil CSRF token
            nama_pembeli: $('input[name="nama_pembeli"]').val(),
            metode_pembayaran: $('select[name="metode_pembayaran"]').val(),
            status_pembayaran: $('select[name="status_pembayaran"]').val(),
            produk: produkIds,  // Pastikan ini berbentuk array
            jumlah: jumlahProduk,  // Pastikan ini berbentuk array
            total_harga: totalHarga
        };

        $.ajax({
            url: '{{ route("pos.proses-pembayaran") }}',
            method: 'POST',
            contentType: "application/json",
            data: JSON.stringify(formData), // Konversi ke JSON
            success: function(response) {
                tampilkanStruk(response.pembelian_id);
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('Gagal memproses pembayaran');
                }
            }
        });
    });

    // Modifikasi fungsi tampilkanStruk
    function tampilkanStruk(pembelianId) {
        $.get(`/pos/cetak-struk/${pembelianId}`, function(html) {
            // Reset keranjang
            keranjang = [];
            updateKeranjang();

            // Tampilkan modal struk
            $('#struk-content').html(html);
            $('#struk-modal').modal('show');
        });
    }
});

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
