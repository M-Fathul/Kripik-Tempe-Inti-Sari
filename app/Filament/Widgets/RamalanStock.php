<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\ForecastProduk;

class RamalanStock extends ApexChartWidget
{
    use InteractsWithPageFilters;
    use HasFiltersSchema;

    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 3;

    

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('filter')
                ->label('Granularitas')
                ->options([
                    'tanggal' => 'Perhari',
                    'week_number' => 'Perminggu',
                    'month_name' => 'Perbulan',
                    'year' => 'Pertahun',
                ])
                ->default('tanggal')
                ->native(false)
                ->required()
                ->selectablePlaceholder(false)
                ->reactive()
                ->preload(),
        ]);
    }

    public function updatedInteractsWithSchemas(string $statePath): void
    {
        $this->updateOptions();
    }

    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'ramalanStock';


    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Grafik Ramalan Stok';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $filter = $this->filters['filter'];
        $produk = $this->pageFilters['produk_id'] ?? null;

        if (!$produk) {
            return [
                'series' => [],
                'chart' => [
                    'type' => 'line',
                    'height' => 400,
                ],
                'xaxis' => [
                    'categories' => [],
                ],
            ];
        }
        

        $q = ForecastProduk::query()
            ->when(
                $produk,
                fn($q) =>
                $q->where('produk_id', $produk)
            );
        if ($filter === 'week_number') {
            $q->selectRaw('
                 year,
                 week_number,
                 CONCAT(LPAD(week_number, 2, "0"), "/", year) as tanggal,
                 SUM(forecast_qyt) as forecast_qyt,
                 SUM(aktual_qyt) as aktual_qyt,
                 SUM(lower) as lower,
                 SUM(upper) as upper
             ')
                ->groupBy('year', 'week_number');
        } elseif ($filter === 'month_name') {
            $q->selectRaw('
                 year,
                 month_name,
                 CONCAT(month_name, " ", year) as tanggal,
                 SUM(forecast_qyt) as forecast_qyt,
                 SUM(aktual_qyt) as aktual_qyt,
                 SUM(lower) as lower,
                 SUM(upper) as upper
             ')
                ->groupBy('year', 'month_name');
        } elseif ($filter === 'year') {
            $q->selectRaw('
                 year as tanggal,
                 SUM(forecast_qyt) as forecast_qyt,
                 SUM(aktual_qyt) as aktual_qyt,
                 SUM(lower) as lower,
                 SUM(upper) as upper
             ')
                ->groupBy('year');
        }

        $data = $q->get();
        //dd($data);
        if ($filter === 'tanggal') {
            $show = false;
        } else {
            $show = true;
        }
        return [
            'series' => [

                [
                    'type' => 'line',
                    'name' => 'Stok Terjual',
                    'data' => $data->map(function ($item) {
                        return [
                            'x' => $item->tanggal,
                            'y' => $item->aktual_qyt,
                        ];
                    }),
                ],
                [
                    'type' => 'line',
                    'name' => 'Ramalan Stok Terjual',
                    'data' => $data->map(function ($item){
                        return [
                            'x' => $item->tanggal,
                            'y' => $item->forecast_qyt,
                        ];
                    }),
                ],
                [
                    'type' => 'rangeArea',
                    'name' => 'Rentang Stok Terjual',
                    'data' => $data->map(function ($item) {
                        return [
                            'x' => $item->tanggal,
                            'y' => [$item->lower, $item->upper],
                        ];
                    }),
                ],

            ],
            'chart' => [
                'type' => 'rangeArea',
                'height' => 408,
                'animations' => [
                    'speed' => 300,
                ],
            ],
            'colors' => ['#f59e0b', '#3b82f6', '#3b82f6'],
            'fill' => [
                'opacity' => [1, 1, 0.24],
            ],
            'xaxis' => [
                'labels' => [
                    'show' => $show,
                    'fontFamily' => 'inherit',
                    'rotate' => 0,
                    'style' => [
                        'hideOverlappingLabels' => true,

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
            'stroke' => [
                'curve' => 'smooth',
                'width' => [2, 2, 0],
            ],
            'forecastDataPoints' => [
                'count' => $data->whereNull('aktual_qyt')->count(),
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'tooltip' => [
                'shared' => true,
                'inverseOrder' => true,
                'onDatasetHover' => [
                    'highlightDataSeries' => true,
                ],
            ],
        ];
    }

}
