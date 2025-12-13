<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use App\Models\Transaksi;
use App\Models\Produk;

class Dashboard extends BaseDashboard
{
    // protected string $view = 'filament.pages.dashboard';

    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        $tanggalawal = Transaksi::min('tanggal_transaksi') ?: now();
        $tanggalakhir = Transaksi::max('tanggal_transaksi') ?: now();
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('produk_id')
                            ->label('Produk')
                            ->options(Produk::pluck('nama_produk', 'id'))
                            ->searchable()
                            ->reactive()
                            ->preload(),
                        DatePicker::make('startDate')
                            ->label('Tanggal Awal')
                            ->native(false)
                            ->reactive()
                            ->minDate($tanggalawal)
                            ->maxDate($tanggalakhir)
                            ->default($tanggalawal)
                            ->closeOnDateSelection(),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->reactive()
                            ->minDate(fn(Get $get) => $get('startDate'))
                            ->maxDate($tanggalakhir)
                            ->default($tanggalakhir)
                            ->native(false)
                            ->closeOnDateSelection(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
