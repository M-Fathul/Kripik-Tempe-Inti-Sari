<?php

namespace App\Filament\Widgets;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class StokTerjual extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'stokTerjual';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Stok Produk Terjual';
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 2;

    use InteractsWithPageFilters;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $startDate = Carbon::parse($this->pageFilters['startDate']);

        $endDate = Carbon::parse($this->pageFilters['endDate']);

        $produk = $this->pageFilters['produk_id'] ?? null;

        $q = Transaksi::query()
            ->leftJoin('produks', 'produks.id', '=', 'transaksis.produk_id')
            ->when(
                $startDate,
                fn($q) =>
                $q->whereDate('tanggal_transaksi', '>=', $startDate)
            )
            ->when(
                $endDate,
                fn($q) =>
                $q->whereDate('tanggal_transaksi', '<=', $endDate)
            )
            ->when(
                $produk,
                fn($q) =>
                $q->where('produk_id', $produk)
            );

        $data = $q->selectRaw("
                COALESCE(produks.nama_produk,'Produk Terhapus') as nama_produk,
                SUM(quantity) as total_terjual
            ")
            ->groupBy('nama_produk')
            ->orderByDesc('total_terjual')
            ->get();
        
        $labels = $data->pluck('nama_produk')->toArray();
        $series = $data->pluck('total_terjual')->toArray();

        $height = max(count($labels) * 40, 400);
        return [
            'chart' => [
                'type' => 'bar',    
                'height' => $height,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Stok Terjual',
                    'data' => $series,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
                'labels' => [
                    'show' => false,
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                    'barHeight' => '70%',
                ],
            ],
            
        ];
    }
}
