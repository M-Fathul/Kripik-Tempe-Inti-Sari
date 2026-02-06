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

        $omset = Transaksi::query()
            ->when($startDate, fn($query) => $query->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->sum('total');

        $omsetawal = Transaksi::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->orderBy('tanggal')
            ->when($startDate, fn($q) => $q->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total) as omset')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->value('omset');

        $omsetakhir = Transaksi::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->orderBy('tanggal', 'desc')
            ->when($endDate, fn($q) => $q->where('tanggal_transaksi', '<=', $endDate))
            ->when($startDate, fn($q) => $q->where('tanggal_transaksi', '>=', $startDate))
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(total) as omset')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->value('omset');

        $terjual = Transaksi::query()
            ->when($startDate, fn($query) => $query->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->sum('quantity');

        $terjualawal = Transaksi::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->orderBy('tanggal')
            ->when($startDate, fn($q) => $q->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(quantity) as quantity')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->value('quantity');

        $terjualakhir = Transaksi::query()
            ->when($produk, fn($q) => $q->where('produk_id', $produk))
            ->orderBy('tanggal', 'desc')
            ->when($endDate, fn($q) => $q->where('tanggal_transaksi', '<=', $endDate))
            ->when($startDate, fn($q) => $q->where('tanggal_transaksi', '>=', $startDate))
            ->selectRaw('DATE(tanggal_transaksi) as tanggal, SUM(quantity) as quantity')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->value('quantity');

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

        $color = function ($awal, $akhir): string {
            if ($awal < $akhir) {
                return 'success';
            } elseif ($awal > $akhir) {
                return 'danger';
            }
            return '';
        };

        $panah = function ($awal, $akhir): string {
            if ($awal < $akhir) {
                return 'heroicon-m-arrow-trending-up';
            } elseif ($awal > $akhir) {
                return 'heroicon-m-arrow-trending-down';
            }
            return 'heroicon-m-arrow-right';
        };

        $ringkas = function ($number): string {
            if (!$number) {
                return 0;
            }
            if ($number < 1000) {
                return Number::format($number, 0);
            }

            if ($number < 1000000) {
                return Number::format($number / 1000, 1) . 'rb';
            }

            if ($number < 1000000000) {
                return Number::format($number / 1000000, 1) . 'jt';
            }
            return Number::format($number / 1000000000, 1) . 'M';
        };

        $persentase = function ($awal, $akhir): string {
            if ($awal == 0) {
                return '';
            }
            $selisih = $akhir - $awal;
            $percent = ($selisih / $awal) * 100;
            return number_format($percent, 0) . '%';
        };

        $chardata = function ($awal, $akhir) {
            if ($awal == $akhir) {
                return [5, 5, 5, 5, 5, 5, 5, 5];
            } elseif ($awal > $akhir) {
                return [9, 6, 7, 4, 5, 2, 3, 0];
            }
            return [0, 3, 2, 5, 4, 7, 6, 9];
        };



        return [
            Stat::make('Omset', 'Rp. ' . $formatNumber($omset))
                ->descriptionIcon($panah($omsetawal, $omsetakhir))
                ->description('awal periode ' . $ringkas(900000000) . ' akhir periode ' . $ringkas(900000000) . ' ' . $persentase($omsetawal, $omsetakhir))
                ->chart($chardata($omsetawal, $omsetakhir))
                ->color($color($omsetawal, $omsetakhir))
                ->reactive(),
            Stat::make('Terjual', $formatNumber($terjual))
                ->descriptionIcon($panah($terjualawal, $terjualakhir))
                ->description('awal periode ' . $ringkas($terjualawal) . ' akhir periode ' . $ringkas($terjualakhir) . ' ' . $persentase($terjualawal, $terjualakhir))
                ->chart($chardata($terjualawal, $terjualakhir))
                ->color($color($terjualawal, $terjualakhir))
                ->reactive(),
            Stat::make('Jenis Produk', $formatNumber($jumlahJenisProduk))
                ->description($descjenisproduk)
                ->reactive(),
        ];
    }
}