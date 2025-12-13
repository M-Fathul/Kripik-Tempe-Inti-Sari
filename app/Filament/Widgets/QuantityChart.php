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

        $activeGroupColumn = $this->filter ?? 'tanggal_transaksi';
        $startDate = !is_null($this->pageFilters['startDate'] ?? null) ?
            Carbon::parse($this->pageFilters['startDate']) :
            (Transaksi::min('tanggal_transaksi') ?: now());

        $endDate = !is_null($this->pageFilters['endDate'] ?? null) ?
            Carbon::parse($this->pageFilters['endDate']) :
            (Transaksi::max('tanggal_transaksi') ?: now());

        $produk = $this->pageFilters['produk_id'] ?? false;      

        $data = Transaksi::query()
            ->when($startDate, fn($query) => $query->where('tanggal_transaksi', '>=', $startDate))
            ->when($endDate, fn($query) => $query->where('tanggal_transaksi', '<=', $endDate))
            ->when($produk, fn($query) => $query->where('produk_id', $produk))
            ->selectRaw("$activeGroupColumn as label, SUM(quantity) as total_quantity")
            ->groupBy("$activeGroupColumn")
            ->orderBy('label', 'asc')
            ->get();
        
        $labels = $data->pluck('label');
        if ($activeGroupColumn === 'tanggal_transaksi') {
        $labels = $labels->map(function ($dateString) {
            return Carbon::parse($dateString)->format('d M y');
        });
    } 
        $dataset = $data->pluck('total_quantity');

        return [
            'datasets' => [
                [
                    'label' => 'Stok terjual per-' . $activeGroupColumn . ' tahunan' ,
                    'data' => $dataset
                ],
            ],
            'labels' => $labels
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
