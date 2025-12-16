<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogController;

Route::get('/', function () {
    return view('user.homepage');
});
Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');