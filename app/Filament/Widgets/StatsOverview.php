<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use App\Models\Produk;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;


class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {

        $startDate = !is_null($this->pageFilters['startDate'] ?? null) ?
            Carbon::parse($this->pageFilters['startDate']) :
            (Transaksi::min('tanggal_transaksi') ?: now());

        $endDate = !is_null($this->pageFilters['endDate'] ?? null) ?
            Carbon::parse($this->pageFilters['endDate']) :
            (Transaksi::max('tanggal_transaksi') ?: now());

        $produk = $this->pageFilters['produk_id'] ?? false;

        $profit = Transaksi::query()
            ->when($startDate, fn($query) => $query->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->sum('total');

        $profitawal = Transaksi::query()
            ->whereDate('tanggal_transaksi', '<=', $startDate)
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->sum('total');

        $terjual = Transaksi::query()
            ->when($startDate, fn($query) => $query->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->sum('quantity');

        $terjualtawal = Transaksi::query()
            ->whereDate('tanggal_transaksi', '<=', $startDate)
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->sum('quantity');

        $jumlahJenisProduk = $produk
            ? 1
            : Transaksi::query()
                ->when($startDate, fn($query) => $query->where('tanggal_transaksi', '>=', $startDate))
                ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
                ->distinct('produk_id')
                ->count('produk_id');

        $descjenisproduk = $produk
            ? Produk::find($this->pageFilters['produk_id'])->nama_produk . ' terjual di periode ini'
            : 'Jumlah jenis produk yang terjual di periode ini';

        $formatNumber = function (int $number): string {
            return Number::format($number);
        };

        $color = function (int $awal, int $akhir): string {
            if ($awal == $akhir) {
                return '';
            } elseif ($awal < $akhir) {
                return 'success';
            }
            return 'danger';
        };

        $panah = function (int $awal, int $akhir): string {
            if ($awal == $akhir) {
                return 'heroicon-m-arrow-right';
            } elseif ($awal < $akhir) {
                return 'heroicon-m-arrow-trending-up';
            }
            return 'heroicon-m-arrow-trending-down';
        };

        $ringkas = function (int $number): string {
            if ($number < 1000) {
                return (string) Number::format($number, 1);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 1) . 'rb';
            }

            return Number::format($number / 1000000, 1) . 'jt';
        };

        $persentase = function (int $awal, int $akhir): string {
            if ($awal == $akhir) {
                return '0%';
            } elseif ($awal == 0) {
                return '100%';
            }
            $diff = $akhir - $awal;
            $percent = ($diff / $awal) * 100;
            return number_format($percent, 0) . '%';
        };

        $chardata = function ($awal, $akhir) {
            if ($awal == $akhir) {
                return [5,5,5,5,5,5,5,5];
            } elseif ($awal > $akhir) {
                return [9,6,7,4,5,2,3,0];
            }
            return [0, 3, 2, 5, 4, 7, 6, 9];
        };



        return [
            Stat::make('Profit', 'Rp. ' . $formatNumber($profit))
                ->descriptionIcon($panah($profitawal, $profit))
                ->description('Kurang lebih dari ' . $ringkas($profitawal) . ' ke ' . $ringkas($profit) . ' ' . $persentase($profitawal, $profit))
                ->chart($chardata($profitawal, $profit))
                ->color($color($profitawal, $profit))
                ->reactive(),
            Stat::make('Terjual', $formatNumber($terjual))
                ->descriptionIcon($panah($terjualtawal, $terjual))
                ->description('Kurang lebih dari ' . $terjualtawal . ' ke ' . $terjual . ' ' . $persentase($terjualtawal, $terjual))
                ->chart($chardata($terjualtawal, $terjual))
                ->color($color($terjualtawal, $terjual))
                ->reactive(),
            Stat::make('Jenis Produk', $formatNumber($jumlahJenisProduk))
                ->description($descjenisproduk)
                ->reactive(),
        ];
    }
}