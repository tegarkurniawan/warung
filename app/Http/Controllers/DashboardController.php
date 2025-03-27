<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil data statistik
        $totalProduk = Produk::count();
        $totalKategori = Kategori::count();
        $totalUser = User::count();

        // Filter pembelian berdasarkan role pengguna
        $pembelianQuery = Pembelian::query();
        if (Auth::user()->role === 'kasir') {
            $pembelianQuery->where('created_by', Auth::user()->email);
        }
        $totalPembelian = $pembelianQuery->count();

        // Mengambil data penjualan 7 hari terakhir
        $labels = [];
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');
           
            $totalPenjualan = Pembelian::when(Auth::user()->role === 'kasir', function ($query) {
                return $query->where('created_by', Auth::user()->email);
            })
            ->whereDate('created_at', $date->toDateString())
                ->sum('total_harga');
        
            
            $data[] = $totalPenjualan;
        }
   
     
        // Mengambil produk dengan stok kurang dari 10 (stok hampir habis)
        $lowStockProducts = Produk::with('kategoris')
            ->where('stok', '<', 10)
            ->where('stok', '>=', 0)
            ->orderBy('stok', 'asc')
            ->get();

        return view('dashboard', compact(
            'totalProduk',
            'totalKategori',
            'totalUser',
            'totalPembelian',
            'labels',
            'data',
            'lowStockProducts'
        ));
    }
}
