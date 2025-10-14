<?php

namespace App\Filament\Resources\WellBeingScores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ImportAction;
use App\Filament\Imports\WellBeingScoreImporter;

class WellBeingScoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain.name')
                    ->sortable(),
                TextColumn::make('timeframe')
                    ->sortable(),
                TextColumn::make('location.name'),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ImportAction::make()
                    ->importer(WellBeingScoreImporter::class)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
