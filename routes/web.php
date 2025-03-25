<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Middleware\CheckRole;

// Route utama
Route::get('/', [AuthController::class, 'index'])->name('index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth'])->group(function() {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk Admin
    Route::middleware([CheckRole::class . ':admin'])->group(function() {
        Route::get('/pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('/pembelian/{id}', [PembelianController::class, 'detail'])->name('pembelian.detail');
    });

    // Route untuk Kasir dan Admin
    Route::middleware([CheckRole::class . ':kasir,admin'])->group(function() {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class);

        Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('/pos/produk-kategori/{kategori_id}', [POSController::class, 'getProdukByKategori']);
        Route::post('/pos/proses-pembayaran', [POSController::class, 'prosesPembayaran'])->name('pos.proses-pembayaran');
        Route::get('/pos/cetak-struk/{pembelianId}', [POSController::class, 'cetakStruk']);
    });
});
