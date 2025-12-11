<?php

namespace App\Filament\Resources\Produks\RelationManagers;

use App\Filament\Resources\Transaksis\TransaksiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TransaksiRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksi';

    protected static ?string $relatedResource = TransaksiResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
