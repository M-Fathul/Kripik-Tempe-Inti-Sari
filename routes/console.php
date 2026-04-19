<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Produk;
use \App\Models\ForecastRun;
use App\Jobs\ForecastProdukJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {

    $produks = Produk::where('status', 'aktif')->get();

    foreach ($produks as $produk) {
        $latestRun = ForecastRun::where('produk_id', $produk->id)
            ->latest()
            ->first();

        if ($latestRun && now()->month === $latestRun->created_at->month) {
            continue;
        }
        ForecastProdukJob::dispatch($produk->id, 30);
    }

})->monthlyOn(1, '00:00');