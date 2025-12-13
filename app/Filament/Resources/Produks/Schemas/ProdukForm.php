<?php

namespace App\Filament\Resources\Produks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProdukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image(),
                TextInput::make('nama_produk')
                    ->required(),
                TextInput::make('harga_produk')
                    ->required()
                    ->prefix('Rp.')
                    ->numeric(),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_terjual')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('kategori_id')
                    ->relationship('kategori', 'nama_kategori')
                    ->preload()
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('nama_kategori')
                            ->required(),
                    ]),
                Select::make('status')
                    ->options(['aktif' => 'Aktif', 'tidak_lanjut' => 'Tidak lanjut'])
                    ->default('aktif')
                    ->required(),
                Select::make('pemasok')
                    ->options(['orisinil' => 'Orisinil', 'eksternal' => 'Eksternal'])
                    ->default('orisinil')
                    ->required(),
            ]);
    }
}
