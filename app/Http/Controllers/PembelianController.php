<?php
namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelian = Pembelian::select('id', 'nama_pembeli', 'total_harga', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembelian.index', compact('pembelian'));
    }

    public function detail($id)
    {
        $pembelian = Pembelian::with('detail.produk')
            ->findOrFail($id);

        return view('pembelian.detail', compact('pembelian'));
    }
}
