<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Pembelian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $produkCount = Produk::count();
        $kategoriCount = Kategori::count();
        $pembelianCount = Pembelian::count();

        // Statistik pembelian per hari (7 hari terakhir)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $dailyStats = Pembelian::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_harga) as total')
        )
        ->where('created_at', '>=', $startDate)
        ->where('created_at', '<=', $endDate)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Format untuk chart
        $labels = [];
        $data = [];

        // Isi data untuk 7 hari
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d M');

            $found = false;
            foreach ($dailyStats as $stat) {
                if ($stat->date == $date) {
                    $data[] = $stat->total;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $data[] = 0;
            }
        }

        return view('dashboard', compact('produkCount', 'kategoriCount', 'pembelianCount', 'labels', 'data'));
    }
}
