<?php

namespace App\Jobs;

use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ForecastService;
use App\Models\ForecastRun;
use App\Models\ForecastProduk;


class ForecastProdukJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $produkID;
    public $periods;
    
    /**
     * Create a new job instance.
     */
    public function __construct($produkID, $periods = 30)
    {
        $this->produkID = $produkID;
        $this->periods = $periods;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $produk = Produk::find($this->produkID);
        $transaksis = Transaksi::where('produk_id', $this->produkID)
            ->selectRaw('
                    DATE(tanggal_transaksi) as tanggal_transaksi,
                    SUM(quantity) as quantity
                ')
                    ->groupByRaw('DATE(tanggal_transaksi)')
                    ->orderByRaw('DATE(tanggal_transaksi)')
            ->get();
        if ($transaksis->isEmpty()) {
            return;
        }

        $service = new ForecastService();
        $data = $service->transformData($transaksis);
        $result = $service->forecast($produk, $data, $this->periods);

        $run = ForecastRun::create([
            'produk_id' => $produk->id,
            'mape' => $result['mape'],
            'insight' => $result['insight'],
            'train_start' => $result['train_start'],
            'train_end' => $result['train_end'],
            'periods' => $this->periods,
        ]);

        foreach ($result['forecast'] as $row) {
            ForecastProduk::updateOrCreate([
                'produk_id' => $produk->id,
                'forecast_run_id' => $run->id,
                'tanggal' => $row['ds']
                ],
                [
                'forecast_qyt' => $row['yhat'],
                'upper' => $row['yhat_upper'],
                'lower' => $row['yhat_lower'],
            ]);
        }
    }
}
