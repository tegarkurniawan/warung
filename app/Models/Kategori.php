<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'gambar'];

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'produk_kategoris');
    }
}
