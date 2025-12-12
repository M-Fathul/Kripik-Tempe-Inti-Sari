<?php

namespace App\Filament\Resources\Kategoris\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Slider;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Produk;

class ProdukRelationManager extends RelationManager
{
    protected static string $relationship = 'produk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->image(),
                TextInput::make('nama_produk')
                    ->required(),
                TextInput::make('harga_produk')
                    ->required()
                    ->numeric(),
                TextInput::make('stok')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_terjual')
                    ->required()
                    ->numeric()
                    ->default(0),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_produk')
            ->columns([
                TextColumn::make('nama_produk')
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
                TrashedFilter::make()
                    ->label('Terhapus')
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
