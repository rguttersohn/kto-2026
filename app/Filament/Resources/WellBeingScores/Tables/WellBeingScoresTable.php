<?php

namespace App\Filament\Resources\WellBeingScores\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ImportAction;
use App\Filament\Imports\WellBeingScoreImporter;
use App\Filament\Support\UIPermissions;
use App\Models\WellBeingScore;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Import;
use Filament\Tables\Grouping\Group;
use App\Models\Location;
use App\Models\LocationType;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;

class WellBeingScoresTable
{
    public static function configure(Table $table): Table
    {
       
        return $table
            ->modifyQueryUsing(fn(Builder $query)=>$query
                ->select('well_being_index.scores.*')
                ->addSelect('locations.locations.name as location_name')
                ->addSelect('app.imports.file_name')
                ->join('locations.locations', 'locations.locations.id', 'well_being_index.scores.location_id')
                ->join('app.imports', 'app.imports.id', 'well_being_index.scores.import_id')
            )
            ->columns([
                TextColumn::make('location_name'),
                TextColumn::make('timeframe')
                    ->sortable(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('file_name')
                    ->label('Import Group')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(WellBeingScoreImporter::class)
            ])
            ->filters([
                SelectFilter::make('import_id')
                    ->label('Import Group')
                    ->searchable()
                    ->options(function(){

                        $score_import_ids = WellBeingScore::select('import_id')->distinct('import_id')->get();

                        $import_ids = $score_import_ids->pluck('import_id')->toArray();

                        return Import::whereIn('id', $import_ids)->get()->pluck('file_name', 'id');

                    }),
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->searchable()
                    ->options(function(){

                        $rankable_location_types = LocationType::where('is_rankable', true)->get();

                        $location_type_ids = $rankable_location_types->pluck('id')->toArray();

                        return Location::whereIn('location_type_id', $location_type_ids)->get()->pluck('name', 'id');
                    }),
                    
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('set_published')
                        ->label('Publish')
                        ->action(fn($records)=> $records->each->update(['is_published' => true]))
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    BulkAction::make('set_unpublished')
                        ->label('Unpublish')
                        ->action(fn($records)=> $records->each->update(['is_published' => false]))
                        ->requiresConfirmation()
                        ->color('warning')
                        ->icon('heroicon-o-check-circle'),
                ])
                ->visible(fn()=>UIPermissions::canPublish()),
            ])
            ->groups([
                Group::make('import.file_name')
                    ->label('Import Group')
            ]);
    }
}
