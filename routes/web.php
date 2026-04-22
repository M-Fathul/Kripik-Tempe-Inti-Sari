<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\ForecastRun;
use App\Models\ForecastProduk;
use App\Services\ForecastService;
use App\Jobs\ForecastProdukJob;

Route::get('/', function () {
    return view('user.homepage');
})->name('user.homepage');

Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');

