<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ForecastRun;
use App\Models\ForecastProduk;
use App\Models\Produk;

class StatsForecast extends StatsOverviewWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 1;
    
    protected function getStats(): array
    {
        $produk = $this->pageFilters['produk_id'] ?? null;
        if (!$produk) {
            return [
                Stat::make('Akurasi', '-')
                    ->description('Pilih produk untuk melihat akurasi')
                    ->color('secondary'),
            ];
        }
        $run = ForecastRun::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->latest()
            ->first();
        
        if (!$run) {
            return [
                Stat::make('Akurasi', '-')
                    ->description(Produk::find($this->pageFilters['produk_id'])->nama_produk . ' belum memiliki data forecast')
                    ->color('secondary'),
            ];
        }
        
        $akurasi = $run->akurasi;
        
        $akurasi = function($akurasi) {
            $percentage = $akurasi;
            return number_format($percentage, 1) . '%';
        };

        $aktual = ForecastProduk::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->whereNotNull('aktual_qyt')
            ->sum('aktual_qyt');

        $ramalanmaks = ForecastProduk::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->whereNull('aktual_qyt')
            ->sum('upper');
        
        $ramalanmin = ForecastProduk::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->whereNull('aktual_qyt')
            ->sum('lower');
        
        $ramalan = ForecastProduk::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->whereNotNull('aktual_qyt')
            ->sum('forecast_qyt');

        $future = ForecastProduk::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->whereNull('aktual_qyt')
            ->sum('forecast_qyt');
        
        $insight = $run->insight ?? 'Tidak ada insight';



        $color = function($akurasi) {
            if ($akurasi >= 85) {
                return 'success';
            } elseif ($akurasi >= 75) {
                return 'warning';
            } else {
                return 'danger';
            }
        };
        return [
            
            Stat::make('Akurasi', $akurasi($akurasi))
                ->description('Total Aktual ' . $aktual . ' | Ramalan ' . $ramalan)
                ->color($color($run->akurasi ?? 0)),
            Stat::make('Total Ramalan Kebutuhan ' . $run->periods . ' Hari Kedepan', $future)
                ->description('Total Ramalan Maks ' . $ramalanmaks . ' | Min ' . $ramalanmin)
                ->color($color($run->akurasi ?? 0)),
            Stat::make('Himbauan', $insight['summary'] ?? 'Tidak ada himbauan')
                ->description($insight['reason'] ?? 'Tidak ada alasan himbauan')
                ->color($color($run->akurasi ?? 0))
        ];
    }
}
