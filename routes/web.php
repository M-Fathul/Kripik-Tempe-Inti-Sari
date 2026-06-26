<?php

use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('user.homepage');
})->name('user.homepage');

Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');

Route::get('/health/flask', function () {
    try {
        // Ganti path dari URL /forecast/prophet menjadi /health
        $flaskHealthUrl = str_replace('/forecast/prophet', '/health', config('services.flask.url'));
        $response = Http::timeout(5)->get($flaskHealthUrl);
        if ($response->successful()) {
            return response()->json(['status' => 'ok']);
        }
    } catch (\Exception $e) {
        // ignore
    }
    return response()->json(['status' => 'down'], 503);
});
