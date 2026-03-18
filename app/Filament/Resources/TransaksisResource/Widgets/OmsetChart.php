<?php

namespace App\Filament\Resources\TransaksisResource\Widgets;

use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class OmsetChart extends ApexChartWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';
    
    use HasFiltersSchema;

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

    use InteractsWithPageFilters;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'omsetChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Grafik Omset Penjualan';

    /**
     * Widget Sort Order
     *
     * @var int|null
     */

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
                CONCAT(LPAD(week_number, 2, "0"), "/", year) as label,
                SUM(total) as total_profit
            ')
                ->groupBy('year', 'week_number')
                ->orderBy('year')
                ->orderBy('week_number');

        } elseif ($filter === 'month_name') {

            $q->selectRaw('
                year,
                MONTH(tanggal_transaksi) as month_number,
                CONCAT(LEFT(month_name, 3), " ", year) as label,
                SUM(total) as total_profit
            ')
                ->groupBy('year', 'month_number', 'month_name')
                ->orderBy('year')
                ->orderBy('month_number');

        } elseif ($filter === 'year') {

            $q->selectRaw('
                year as label,
                SUM(total) as total_profit
            ')
                ->groupBy('year')
                ->orderBy('year');

        } else {
            $q->selectRaw('
                DATE(tanggal_transaksi) as label,
                SUM(total) as total_profit
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
                'height' => 500,
            ],
            'series' => [
                [
                    'name' => 'Omset',
                    'data' => $data->pluck('total_profit'),
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

            'colors' => ['#f59e0b'],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ]
        ];
    }
    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
    {
        yaxis: {
            labels: {
                formatter: function (val) {
                    return 'Rp.' + val.toLocaleString('id-ID');
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Rp.' + val.toLocaleString('id-ID');
                }
            }
        },
    }
    JS);
    }
}
