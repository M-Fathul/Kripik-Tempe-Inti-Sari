<?php

namespace App\Filament\Resources\Produks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Produk;

class ProduksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_produk')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('harga_produk')
                    ->label('Harga')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('stok')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_terjual')
                    ->label('Terjual')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('kategori.nama_kategori')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('pemasok')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Terakhir diperbarui')
                    ->dateTime('d M, Y')
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('stok')
                    ->form([
                        TextInput::make('stok_min')
                            ->label('Stok Minimal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(function () {
                                return Produk::max('stok');
                            }),
                        TextInput::make('stok_max')
                            ->label('Stok Maksimal')
                            ->numeric()
                            ->minValue(0)
                            ->default(function () {
                                return Produk::max('stok');
                            })
                            ->maxValue(function () {
                                return Produk::max('stok');
                            }),
                        
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereBetween('stok', [$data['stok_min'], $data['stok_max']]);
                    }),
                Filter::make('total_terjual')
                    ->form([
                        TextInput::make('total_terjual_min')
                            ->label('Total Terjual Minimal')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(function () {
                                return Produk::max('total_terjual');
                            }),
                        TextInput::make('total_terjual_max')
                            ->label('Total Terjual Maksimal')
                            ->numeric()
                            ->minValue(0)
                            ->default(function () {
                                return Produk::max('total_terjual');
                            })
                            ->maxValue(function () {
                                return Produk::max('total_terjual');
                            }),
                        
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->whereBetween('total_terjual', [$data['total_terjual_min'], $data['total_terjual_max']]);
                    }),
                SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama_kategori')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak_lanjut' => 'Tidak lanjut',
                    ]),
                SelectFilter::make('Pemasok')
                    ->options([
                        'orisinil' => 'Orisinil',
                        'eksternal' => 'Eksternal',
                    ]),
                TrashedFilter::make()
                    ->label('Terhapus')
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
