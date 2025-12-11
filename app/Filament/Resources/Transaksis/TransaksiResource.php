<?php

namespace App\Filament\Resources\Transaksis;

use App\Filament\Resources\Transaksis\Pages\ManageTransaksis;
use App\Models\Transaksi;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

use App\Models\Produk;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal_transaksi')
                    ->required()
                    ->native(false)
                    ->reactive()
                    ->format('d/m/Y')
                    ->default(now())
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (empty($state)) {
                            $set('day_of_week', null);
                            $set('month_name', null);
                            $set('year', null);
                            $set('quarter', null);
                            $set('week_number', null);
                            return;
                        }

                        try {
                            if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $state)) {
                                $dt = Carbon::createFromFormat('d/m/Y', $state);
                            } else {
                                $dt = Carbon::parse($state);
                            }
                        } catch (\Exception $e) {
                            return;
                        }

                        $set('day_of_week', $dt->dayName);
                        $set('month_name', $dt->monthName);
                        $set('year', $dt->year);
                        $set('quarter', $dt->quarter);
                        $set('week_number', $dt->weekOfMonth);
                    }),
                Select::make('produk_id')
                    ->relationship('produk', 'nama_produk')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $produk = $state ? Produk::find($state) : null;
                        $quantity = $get('quantity') ?? 0;
                        $set('total', $produk ? $produk->harga_produk * $quantity : 0);
                    }),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->reactive()
                    ->default(1)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $produk = Produk::find($get('produk_id'));
                        $set('total', $produk ? $produk->harga_produk * $state : 0);
                    }),
                TextInput::make('total')
                    ->readOnly()
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('Rp '),

                TextInput::make('day_of_week')
                    ->label('Hari')
                    ->required()
                    ->default(now()->dayName),
                TextInput::make('month_name')
                    ->label('Bulan')
                    ->required()
                    ->default(now()->monthName),
                TextInput::make('year')
                    ->label('Tahun')
                    ->required()
                    ->numeric()
                    ->default(now()->year),
                TextInput::make('quarter')
                    ->label('Kuartil Tahun ke-')
                    ->required()
                    ->numeric()
                    ->default(now()->quarter),
                TextInput::make('week_number')
                    ->label('Minggu ke-')
                    ->required()
                    ->numeric()
                    ->default(now()->weekOfMonth),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tanggal_transaksi')
                    ->dateTime(),
                TextEntry::make('produk.nama_produk')
                    ->label('Produk'),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('total')
                    ->money('IDR'),
                TextEntry::make('day_of_week')
                    ->label('Hari'),
                TextEntry::make('month_name')
                    ->label('Bulan'),
                TextEntry::make('year')
                    ->label('Tahun'),
                TextEntry::make('quarter')
                    ->numeric()
                    ->label('Kuartil Tahun ke-'),
                TextEntry::make('week_number')
                    ->numeric()
                    ->label('Minggu ke-'),
                TextEntry::make('updated_at')
                    ->dateTime('d F, Y H:i')
                    ->placeholder('-')
                    ->label('Terakhir Diperbarui'),
                TextEntry::make('deleted_at')
                    ->label('Dihapus pada')
                    ->dateTime()
                    ->visible(fn(Transaksi $record): bool => $record->trashed()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                            ->minDate(fn ($get) => $get('created_from'))
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
                
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTransaksis::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
