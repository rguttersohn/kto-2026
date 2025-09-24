<?php

namespace App\Filament\Resources\Indicators\RelationManagers;

use App\Filament\Resources\Indicators\IndicatorResource;
use Dom\Text;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class DataRelationManager extends RelationManager
{
    protected static string $relationship = 'data';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('data')
                    ->numeric(),
                TextColumn::make('dataFormat.name'),
                TextColumn::make('timeframe'),
                TextColumn::make('location.name'),
                TextColumn::make('breakdown.name'),
                IconColumn::make('is_published')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
