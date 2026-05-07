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
use App\Models\User;
use Filament\Notifications\Notification;

class ForecastProdukJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 10, 10];
    public $timeout = 120;

    public $produkID;
    public $periods;

    public function __construct($produkID, $periods)
    {
        $this->produkID = $produkID;
        $this->periods = $periods;
    }

    public function handle(): void
    {
        $produk = Produk::find($this->produkID);
        $transaksis = Transaksi::where('produk_id', $this->produkID)
            ->selectRaw('tanggal_transaksi, SUM(quantity) as quantity')
            ->groupBy('tanggal_transaksi')
            ->orderBy('tanggal_transaksi')
            ->get();

        if ($transaksis->isEmpty()) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Forecast Gagal')
                    ->body("Produk ID {$this->produkID} tidak memiliki data transaksi untuk forecasting.")
                    ->danger()
                    ->sendToDatabase($admin);
            }
            return; 
        }

        $service = new ForecastService();
        $data = $service->transformData($transaksis);
        $result = $service->forecast($data, $this->periods);

        ForecastRun::where('produk_id', $produk->id)->delete();
        $run = ForecastRun::create([
            'produk_id' => $produk->id,
            'mape'        => $result['mape'],
            'insight'     => $result['insight'],
            'train_start' => $result['train_start'],
            'train_end'   => $result['train_end'],
            'periods'     => $this->periods,
        ]);

        foreach ($result['validation'] as $row) {
            ForecastProduk::updateOrCreate(
                ['produk_id' => $produk->id, 'forecast_run_id' => $run->id, 'tanggal' => $row['ds']],
                ['month_name' => $row['month'], 'week_number' => $row['week'], 'year' => $row['year'],
                 'forecast_qyt' => $row['yhat'], 'aktual_qyt' => $row['aktual']]
            );
        }

        foreach ($result['forecast'] as $row) {
            ForecastProduk::updateOrCreate(
                ['produk_id' => $produk->id, 'forecast_run_id' => $run->id, 'tanggal' => $row['ds']],
                ['month_name' => $row['month'], 'week_number' => $row['week'], 'year' => $row['year'],
                 'forecast_qyt' => $row['yhat'], 'upper' => $row['yhat_upper'], 'lower' => $row['yhat_lower']]
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::critical("ForecastProdukJob permanently failed", [
            'produk_id' => $this->produkID,
            'error'     => $exception->getMessage(),
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('Forecast Gagal')
                ->body("Produk ID {$this->produkID} gagal disimpan setelah 3 percobaan.")
                ->danger()
                ->sendToDatabase($admin);
        }
    }
}
