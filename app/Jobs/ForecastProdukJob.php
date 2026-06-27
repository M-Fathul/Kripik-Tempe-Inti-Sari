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
            ->get()
            ->map(fn($item) => [
                'tanggal_transaksi' => $item->tanggal_transaksi,
                'quantity' => (int) $item->quantity
            ])
            ->toArray();

        if (empty($transaksis)) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Forecast Gagal')
                    ->body("Produk {$produk->nama_produk} tidak memiliki data transaksi untuk forecasting.")
                    ->danger()
                    ->sendToDatabase($admin);
            }
            return;
        }

        $service = new ForecastService();
        $result = $service->forecast($transaksis, $this->periods);

        ForecastRun::where('produk_id', $produk->id)->delete();
        $run = ForecastRun::create([
            'produk_id' => $produk->id,
            'akurasi' => $result['akurasi'],
            'insight' => $result['insight'],
            'periods' => $this->periods,
        ]);

        foreach ($result['validation'] as $row) {
            ForecastProduk::updateOrCreate(
                [
                    'produk_id' => $produk->id,
                    'forecast_run_id' => $run->id,
                    'tanggal' => $row['tanggal_transaksi'],
                    'month_name' => $row['month'],
                    'week_number' => $row['week'],
                    'year' => $row['year'],
                ],
                [

                    'forecast_qyt' => $row['prediction'],
                    'aktual_qyt' => $row['aktual']
                ]
            );
        }

        foreach ($result['forecast'] as $row) {
            ForecastProduk::updateOrCreate(
                [
                    'produk_id' => $produk->id,
                    'forecast_run_id' => $run->id,
                    'tanggal' => $row['tanggal_transaksi'],
                    'month_name' => $row['month'],
                    'week_number' => $row['week'],
                    'year' => $row['year'],
                ],
                [
                    'forecast_qyt' => $row['prediction'],
                    'upper' => $row['yhat_upper'] ?? null,
                    'lower' => $row['yhat_lower'] ?? null
                ]
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::critical("ForecastProdukJob permanently failed", [
            'produk_id' => $this->produkID,
            'error' => $exception->getMessage(),
        ]);

        $produk = Produk::find($this->produkID);
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('Forecast Gagal')
                ->body("Produk {$produk->nama_produk} gagal disimpan setelah 3 percobaan.")
                ->danger()
                ->sendToDatabase($admin);
        }
    }
}
