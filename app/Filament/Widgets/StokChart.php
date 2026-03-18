<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class StokChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'stokChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Grafik Stok Terjual';

    use InteractsWithPageFilters;
    use HasFiltersSchema;

    protected static ?int $sort = 2;

    public ?string $filter = 'tanggal_transaksi';

    public function filtersSchema(Schema $schema): Schema
    {
        return  $schema->components([
            Select::make('filter')
            ->label('Periode')
            ->options([
                'tanggal_transaksi' => 'Perhari',
                'week_number' => 'Perminggu',
                'month_name' => 'Perbulan',
                'year' => 'Pertahun',
            ])
            ->default('tanggal_transaksi')
            ->native(false)
            ->required()
            ->selectablePlaceholder(false)
            ->reactive()
        ]);
    }

    public function updatedInteractsWithSchemas(string $statePath): void
    {
        $this->updateOptions();
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $filter = $this->filters['filter'];
        $startDate = Carbon::parse($this->pageFilters['startDate']);

        $endDate = Carbon::parse($this->pageFilters['endDate']);

        $produk = $this->pageFilters['produk_id'] ?? null;

        $q = Transaksi::query()
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

        if ($filter === 'week_number') {

            $q->selectRaw('
                year,
                week_number,
                CONCAT(LPAD(week_number, 2, "0"), "/",year) as label,
                SUM(quantity) as total_quantity
            ')
                ->groupBy('year', 'week_number')
                ->orderBy('year')
                ->orderBy('week_number');

        } elseif ($filter === 'month_name') {

            $q->selectRaw('
                year,
                MONTH(tanggal_transaksi) as month_number,
                CONCAT(LEFT(month_name, 3), " ", year) as label,
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

        if ($filter === 'tanggal_transaksi'){
            $show = false;
        }else{
            $show = true;
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'Stok Terjual',
                    'data' => $data->pluck('total_quantity'),
                ],
            ],
            'xaxis' => [
                'categories' => $data->pluck('label'),
                'labels' => [
                    'show' => $show,
                    'rotate' => 0,
                    'hideOverlappingLabels' => true,
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
        ];
    }
}
