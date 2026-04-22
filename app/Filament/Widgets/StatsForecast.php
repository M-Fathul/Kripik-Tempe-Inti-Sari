<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ForecastRun;
use App\Models\ForecastProduk;

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
        
        $akurasi = function($mape) {
            $percentage = 100 - $mape;
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



        $color = function($mape) {
            if ($mape < 10) {
                return 'success';
            } elseif ($mape < 20) {
                return 'warning';
            } else {
                return 'danger';
            }
        };
        return [
            
            Stat::make('Akurasi', $akurasi($run->mape ?? 0))
                ->description('Aktual ' . $aktual . ' | Ramalan ' . $ramalan)
                ->color($color($run->mape ?? 0)),
            Stat::make('Ramalan Kebutuhan ' . $run->periods . ' Hari Kedepan', $future)
                ->description('Ramalan Maks ' . $ramalanmaks . ' | Min ' . $ramalanmin)
                ->color($color($run->mape ?? 0)),
            Stat::make('Himbauan', $insight['summary'] ?? 'Tidak ada himbauan')
                ->description($insight['reason'] ?? 'Tidak ada alasan himbauan')
                ->color($color($run->mape ?? 0))
        ];
    }
}
