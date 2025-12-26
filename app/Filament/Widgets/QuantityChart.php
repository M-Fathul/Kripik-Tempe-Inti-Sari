<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\LineChartWidget as ChartWidget;

class QuantityChart extends ChartWidget
{

    use InteractsWithPageFilters;
    protected static ?int $sort = 1;
    protected ?string $heading = 'Grafik Stok Terjual';

    protected function getFilters(): ?array
    {
        return [
            'tanggal_transaksi' => 'Perhari',
            'week_number' => 'Perminggu',
            'month_name' => 'Perbulan',
            'year' => 'Pertahun',
        ];
    }

    protected function getDefaultFilters(): ?array
    {
        return [
            'tanggal_transaksi', 
        ];
    }


    protected function getData(): array
    {
        $filter = $this->filter ?? 'tanggal_transaksi';

        $startDate = !empty($this->pageFilters['startDate'])
            ? Carbon::parse($this->pageFilters['startDate'])
            : (Transaksi::min('tanggal_transaksi') ?? now());

        $endDate = !empty($this->pageFilters['endDate'])
            ? Carbon::parse($this->pageFilters['endDate'])
            : (Transaksi::max('tanggal_transaksi') ?? now());

        $produk = $this->pageFilters['produk_id'] ?? null;

        $q = Transaksi::query()
            ->when($startDate, fn ($q) =>
                $q->whereDate('tanggal_transaksi', '>=', $startDate)
            )
            ->when($endDate, fn ($q) =>
                $q->whereDate('tanggal_transaksi', '<=', $endDate)
            )
            ->when($produk, fn ($q) =>
                $q->where('produk_id', $produk)
            );

        if ($filter === 'week_number') {

            $q->selectRaw('
                year,
                week_number,
                CONCAT(year, " - Minggu ", LPAD(week_number, 2, "0")) as label,
                SUM(quantity) as total_quantity
            ')
            ->groupBy('year', 'week_number')
            ->orderBy('year')
            ->orderBy('week_number');

        } elseif ($filter === 'month_name') {

            $q->selectRaw('
                year,
                MONTH(tanggal_transaksi) as month_number,
                CONCAT(month_name, " ", year) as label,
                SUM(quantity) as total_quantity
            ')
            ->groupBy('year', 'month_number', 'month_name')
            ->orderBy('year')
            ->orderBy('month_number');

        } elseif ($filter === 'year') {

            $q->selectRaw('
                year as label,
                SUM(quantity) as total_quantity
            ')
            ->groupBy('year')
            ->orderBy('year');

        } else {
            $q->selectRaw('
                DATE(tanggal_transaksi) as label,
                SUM(quantity) as total_quantity
            ')
            ->groupByRaw('DATE(tanggal_transaksi)')
            ->orderByRaw('DATE(tanggal_transaksi)');
        }

        $data = $q->get();
        if ($filter === 'tanggal_transaksi') {
            $labels = $data->pluck('label')->map(
                fn ($d) => Carbon::parse($d)->format('d M Y')
            );
        } else {
            $labels = $data->pluck('label');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Stok terjual',
                    'data' => $data->pluck('total_quantity'),
                ],
            ],
            'labels' => $labels,
        ];
    }
    protected function getType(): string
    {
        return 'line';
    }
}
