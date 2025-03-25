<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'gambar', 'harga'];

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'produk_kategoris');
    }

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
