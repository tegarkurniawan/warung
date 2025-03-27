<div class="text-center">
    <h4>STRUK PEMBAYARAN</h4>
    <hr>
    <div class="row">
        <div class="col-6 text-start">Nama Pembeli:</div>
        <div class="col-6 text-end">{{ $pembelian->nama_pembeli }}</div>
    </div>
    <div class="row">
        <div class="col-6 text-start">Tanggal:</div>
        <div class="col-6 text-end">{{ $pembelian->created_at->format('d M Y H:i') }}</div>
    </div>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($pembelian->detail as $detail)
            <tr>
                <td>{{ $detail->produk->nama }}</td>
                <td>{{ $detail->jumlah }}</td>
                <td>Rp. {{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td>Rp. {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                @php $total += $detail->harga * $detail->jumlah @endphp
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="col-6 text-start">Total Harga:</div>
        <div class="col-6 text-end">Rp. {{ number_format($total, 0, ',', '.') }}</div>
    </div>
    <div class="row">
        <div class="col-6 text-start">Metode Pembayaran:</div>
        <div class="col-6 text-end">{{ ucfirst($pembelian->metode_pembayaran) }}</div>
    </div>
    <div class="row">
        <div class="col-6 text-start">Status Pembayaran:</div>
        <div class="col-6 text-end">{{ ucfirst($pembelian->status_pembayaran) }}</div>
    </div>
    <hr>
    <p class="text-center">Terima Kasih</p>
</div> 