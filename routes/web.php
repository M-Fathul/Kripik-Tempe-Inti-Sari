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

// Route::get('/test-job/{produk}', function ($produkId) {
//     ForecastProdukJob::dispatch($produkId, 30);

//     return "Job dispatched!";
// });

// Route::get('/test-all', function () {

//     $produks = Produk::where('status', 'aktif')->get();

//     foreach ($produks as $produk) {
//         ForecastProdukJob::dispatch($produk->id, 30);
//     }

//     return "All jobs dispatched!";
// });

// Route::get('/test-forecast/{produk}', function ($produkId) {

//     $produk = Produk::findOrFail($produkId);

//     $transaksis = Transaksi::where('produk_id', $produkId)
//         ->selectRaw('
//                 DATE(tanggal_transaksi) as tanggal_transaksi,
//                 SUM(quantity) as quantity
//             ')
//                 ->groupByRaw('DATE(tanggal_transaksi)')
//                 ->orderByRaw('DATE(tanggal_transaksi)')
//         ->get();

//     if ($transaksis->isEmpty()) {
//         return "Tidak ada data transaksi";
//     }

//     $service = new ForecastService();

//     // transform ke format Prophet
//     $data = $service->transformData($transaksis);

//     // 🔥 CALL FLASK
//     $result = $service->forecast($produk, $data, 30);

//     // ======================
//     // SIMPAN KE DATABASE
//     // ======================

//     $run = ForecastRun::create([
//         'produk_id' => $produk->id,
//         'mape' => $result['mape'],
//         'insight' => $result['insight'],
//         'train_start' => $result['train_start'],
//         'train_end' => $result['train_end'],
//         'periods' => 30,
//     ]);

//     foreach ($result['forecast'] as $row) {
//         ForecastProduk::create([
//             'produk_id' => $produk->id,
//             'forecast_run_id' => $run->id,
//             'tanggal' => $row['ds'],
//             'forecast_qyt' => $row['yhat'],
//             'lower' => $row['yhat_lower'] ?? null,
//             'upper' => $row['yhat_upper'] ?? null,
//         ]);
//     }

//     return [
//         'message' => 'Sukses',
//         'forecast_run_id' => $run->id,
//         'total_saved' => count($result['forecast']),
//     ];
// });