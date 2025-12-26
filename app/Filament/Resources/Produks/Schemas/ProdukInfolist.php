<?php

namespace App\Filament\Resources\Produks\Schemas;

use App\Models\Produk;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProdukInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('image')
                    ->label('Gambar Produk')
                    ->disk('public')        
                    ->visibility('public')   
                    ->height(250)
                    ->columnSpanFull()
                    ->placeholder('Tidak ada gambar'),
                TextEntry::make('nama_produk'),
                TextEntry::make('harga_produk')
                    ->numeric()
                    ->money('IDR'),
                TextEntry::make('stok')
                    ->numeric(),
                TextEntry::make('total_terjual')
                    ->numeric(),
                TextEntry::make('kategori.nama_kategori')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('pemasok')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime('d F, Y')
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime('d F, Y')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn(Produk $record): bool => $record->trashed()),
            ]);
    }
}
