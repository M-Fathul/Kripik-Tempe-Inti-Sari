<?php

namespace App\Filament\Resources\Produks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
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
                    ->label('Stok')
                    ->form([
                        Slider::make('stok')
                            ->label('Range Stok')
                            ->default(function () {
                                $maxStok = Produk::max('stok');
                                return [0, $maxStok];
                            })
                            ->minValue(0)
                            ->maxValue(function () {
                                return Produk::max('stok');
                            })
                            ->step(1)
                            ->tooltips()
                            ->minDifference(1)
                            ->fillTrack([false, true, false]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['stok'])) {
                            return $query;
                        }

                        $stokRange = $data['stok'];

                        if (is_array($stokRange) && count($stokRange) === 2) {
                            $query = $query->whereBetween('stok', [$stokRange[0], $stokRange[1]]);
                        }

                        return $query;
                    }),
                Filter::make('total_terjual')
                    ->label('Terjual')
                    ->form([
                        Slider::make('total_terjual')
                            ->label('Range Terjual')
                            ->default(function () {
                                $maxStok = Produk::max('total_terjual');
                                return [0, $maxStok];
                            })
                            ->minValue(0)
                            ->maxValue(function () {
                                return Produk::max('total_terjual');
                            })
                            ->step(1)
                            ->tooltips()
                            ->minDifference(1)
                            ->fillTrack([false, true, false]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['total_terjual'])) {
                            return $query;
                        }

                        $stokRange = $data['total_terjual'];

                        if (is_array($stokRange) && count($stokRange) === 2) {
                            $query = $query->whereBetween('total_terjual', [$stokRange[0], $stokRange[1]]);
                        }

                        return $query;
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
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
