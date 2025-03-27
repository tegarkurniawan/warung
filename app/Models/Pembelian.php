<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = ['nama_pembeli', 'total_harga', 'metode_pembayaran', 'status_pembayaran','created_by'];

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
