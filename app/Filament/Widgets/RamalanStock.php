<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\ForecastProduk;

class RamalanStock extends ApexChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 3;

    

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('filter')
                ->label('Granularitas')
                ->options([
                    'tanggal' => 'Perhari',
                    'week_number' => 'Perminggu',
                    'month_name' => 'Perbulan',
                    'year' => 'Pertahun',
                ])
                ->default('tanggal')
                ->reactive()
                ->native(false)
                ->required()
                ->selectablePlaceholder(false),
        ]);
    }

    public function updatedInteractsWithSchemas(string $statePath): void
    {
        $this->updateOptions();
    }


    public function mount(): void
    {
        parent::mount();
        $this->filters = request()->query('filters', []);
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
        $produk = data_get($this->filters, 'produk_id', null);
        $filter = $this->filter ?? 'tanggal'; 

        if (!$produk) {
            return ['series' => [], 'chart' => ['type' => 'line', 'height' => 400]];
        }

        $q = ForecastProduk::query()->where('produk_id', $produk);

        if ($filter === 'week_number') {
            $q->selectRaw('year, week_number, CONCAT(LPAD(week_number, 2, "0"), "/", year) as tanggal, SUM(forecast_qyt) as forecast_qyt, SUM(aktual_qyt) as aktual_qyt, SUM(lower) as lower, SUM(upper) as upper')
              ->groupBy('year', 'week_number');
        } elseif ($filter === 'month_name') {
            $q->selectRaw('year, month_name, CONCAT(month_name, " ", year) as tanggal, SUM(forecast_qyt) as forecast_qyt, SUM(aktual_qyt) as aktual_qyt, SUM(lower) as lower, SUM(upper) as upper')
              ->groupBy('year', 'month_name');
        } elseif ($filter === 'year') {
            $q->selectRaw('year as tanggal, SUM(forecast_qyt) as forecast_qyt, SUM(aktual_qyt) as aktual_qyt, SUM(lower) as lower, SUM(upper) as upper')
              ->groupBy('year');
        } else {
            $q->selectRaw('tanggal, forecast_qyt, aktual_qyt, lower, upper');
        }

        $data = $q->get();
        return [
            'series' => [
                ['type' => 'line', 'name' => 'Stok Terjual', 'data' => $data->map(fn($i) => ['x' => $i->tanggal, 'y' => $i->aktual_qyt])],
                ['type' => 'line', 'name' => 'Ramalan Stok Terjual', 'data' => $data->map(fn($i) => ['x' => $i->tanggal, 'y' => $i->forecast_qyt])],
                ['type' => 'rangeArea', 'name' => 'Rentang Stok Terjual', 'data' => $data->map(fn($i) => ['x' => $i->tanggal, 'y' => [$i->lower, $i->upper]])],
            ],
            'chart' => ['type' => 'rangeArea', 'height' => 408],
            'xaxis' => ['labels' => ['show' => ($filter !== 'tanggal')]],
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
