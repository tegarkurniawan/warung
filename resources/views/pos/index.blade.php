@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Kategori dan Produk -->
        <div class="col-md-8">
            <div class="row">
                <!-- Kategori -->
                <div class="col-md-3">
                    <div class="list-group" id="kategori-list">
                    <a href="#" class="list-group-item list-group-item-action kategori-item active"
                           data-kategori-id="all">

                            Semua Kategori
                        </a>
                        @foreach($kategoris as $kategori)
                        <a href="#" class="list-group-item list-group-item-action kategori-item"
                           data-kategori-id="{{ $kategori->id }}">
                            {{ $kategori->nama }}
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Produk -->
                <div class="col-md-9">
                    <div class="row" id="produk-list">
                        @foreach($produks as $produk)
                        <div class="col-md-4 mb-3 produk-item"
                             data-kategori-ids="{{ $produk->kategoris->pluck('id')->implode(',') }}">
                            <div class="card">
                                <img src="{{ asset('storage/produks/' . $produk->gambar) }}" class="card-img-top" alt="{{ $produk->nama }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $produk->nama }}</h5>
                                    <p class="card-text">Rp. {{ number_format($produk->harga, 0, ',', '.') }}</p>
                                    <button class="btn btn-primary tambah-produk"
                                            data-produk-id="{{ $produk->id }}"
                                            data-produk-nama="{{ $produk->nama }}"
                                            data-produk-harga="{{ $produk->harga }}">
                                        Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
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

@section('scripts')
<script>
$(document).ready(function() {
    let keranjang = [];
    let totalHarga = 0;

    // Filter produk berdasarkan kategori
   $('.kategori-item').on('click', function() {
        $('.kategori-item').removeClass('active');
        $(this).addClass('active');

        let kategoriId = $(this).data('kategori-id');

        if (kategoriId === "all") {
            $('.produk-item').show();
        } else {
            $('.produk-item').hide();
            $(`.produk-item[data-kategori-ids*="${kategoriId}"]`).show();
        }
    });


    // Tambah produk ke keranjang
     $(document).on('click', '.tambah-produk', function() {
        let produkId = $(this).data('produk-id');
        let produkNama = $(this).data('produk-nama');
        let produkHarga = $(this).data('produk-harga');

        let existingItem = keranjang.find(item => item.produk_id === produkId);
        if (existingItem) {
            existingItem.jumlah++;
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
            item.jumlah++;
            updateKeranjang();
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
            item.jumlah = jumlahBaru > 0 ? jumlahBaru : 1;
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

            keranjangBody.append(`
                <tr>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary btn-minus" data-produk-id="${item.produk_id}">-</button>
                        <input type="number" class="form-control d-inline input-jumlah" style="width: 50px; text-align: center;"
                            value="${item.jumlah}" min="1" data-produk-id="${item.produk_id}">
                        <button class="btn btn-sm btn-outline-secondary btn-plus" data-produk-id="${item.produk_id}">+</button>
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
            alert('Gagal memproses pembayaran');
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
@endsection
