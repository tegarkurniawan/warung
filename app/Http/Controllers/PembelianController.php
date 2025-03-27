<?php
namespace App\Http\Controllers;

use App\Models\Pembelian;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembelian::with('detail','detail.produk');

        // Filter berdasarkan role pengguna
        if (Auth::user()->role === 'kasir') {
            $query->where('created_by', Auth::user()->email);
        }

        // Pencarian berdasarkan nama pembeli
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('nama_pembeli', 'like', "%{$searchTerm}%");
        }

        // Filter berdasarkan range tanggal
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereDate('created_at', '<=', $endDate);
        }

        $pembelian = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Mempertahankan parameter pencarian dan filter pada pagination links
        if ($request->has('search') || $request->has('start_date') || $request->has('end_date')) {
            $pembelian->appends($request->only(['search', 'start_date', 'end_date']));
        }

        return view('pembelian.index', compact('pembelian'));
    }

    public function detail($id)
    {
        $pembelian = Pembelian::with('detail.produk')
            ->findOrFail($id);

        return view('pembelian.detail', compact('pembelian'));
    }
    
    public function cetakStruk($pembelianId)
    {
        $pembelian = Pembelian::with('detail.produk')->findOrFail($pembelianId);
        return view('pembelian.struk', compact('pembelian'));
    }
}
