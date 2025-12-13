<?php

namespace App\Filament\Widgets;

use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
use App\Models\Transaksi;


class TransaksiTerbaru extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Transaksi::query())
            ->columns([
                TextColumn::make('tanggal_transaksi')
                    ->dateTime('d F, Y')
                    ->sortable(),
                TextColumn::make('produk.nama_produk')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('month_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Bulan'),
                TextColumn::make('year')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Tahun'),
                TextColumn::make('quarter')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Kuartil Tahun ke-'),
                TextColumn::make('week_number')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Minggu ke-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Didata pada'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui pada'),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dihapus pada'),
            ])
            ->filters([
                Filter::make('tanggal_transaksi')
                    ->schema([
                        DatePicker::make('created_from')->label('Dari tanggal transaksi')
                            ->native(false)
                            ->reactive()
                            ->closeOnDateSelection(),
                        DatePicker::make('created_until')->label('Hingga tanggal transaksi')
                            ->native(false)
                            ->reactive()
                            ->minDate(fn($get) => $get('created_from'))
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_transaksi', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_transaksi', '<=', $date),
                            );
                    }),
                SelectFilter::make('produk_id')
                    ->label('Produk')
                    ->relationship('produk', 'nama_produk')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                TrashedFilter::make()
                    ->label('Terhapus')
            ])
            ->recordActions([
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
