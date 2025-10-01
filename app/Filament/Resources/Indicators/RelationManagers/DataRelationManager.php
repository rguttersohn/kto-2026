<?php

namespace App\Filament\Resources\Indicators\RelationManagers;

use App\Filament\Imports\IndicatorDataImporter;
use App\Models\Breakdown;
use App\Models\Scopes\PublishedScope;
use Filament\Actions\ImportAction;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\IndicatorData;
use Filament\Actions\BulkAction;
use Filament\Tables\Grouping\Group;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;


class DataRelationManager extends RelationManager
{
    protected static string $relationship = 'data';

    protected function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
       
    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn() => $this->getRelationship()->getQuery()->withoutGlobalScopes()
            )
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
                ImportAction::make()
                    ->importer(IndicatorDataImporter::class)
                    ->headerOffset(fn($data) => $data['row_offset'] ?? 0)
                    ->options([
                            'indicator_id' => $this->getOwnerRecord()->getKey(),
                            ])
                    ->label('Import Data'),

            ])
            ->filters([
                SelectFilter::make('data_format_id')
                    ->relationship('dataFormat', 'name')
                    ->label('Data Format'),
                SelectFilter::make('breakdown_id')
                    // ->relationship('breakdown', 'name')
                    ->label('Breakdown')
                    ->options(function(){
                        
                        $indicator_id = $this->getOwnerRecord()->id;

                        $breakdown_ids = IndicatorData::where('indicator_id', $indicator_id)
                            ->selectRaw('DISTINCT breakdown_id')
                            ->withoutGlobalScopes()
                            ->get();
                        

                        $breakdowns = Breakdown::select('name', 'id')
                            ->whereIn('id', $breakdown_ids->pluck('breakdown_id'))
                            ->get();

                        $options = [];

                        $breakdowns->each(function($breakdown) use (&$options) {
                            $options[$breakdown->id] = $breakdown->name;
                        });


                        return $options;
                           
                    
                    }),
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->options(function(){
                        $indicator_id = $this->getOwnerRecord()->id;

                        $location_ids = IndicatorData::where('indicator_id', $indicator_id)
                            ->selectRaw('DISTINCT location_id')
                            ->withoutGlobalScopes()
                            ->get();
                        
                        $locations = \App\Models\Location::select('name', 'id')
                            ->whereIn('id', $location_ids->pluck('location_id'))
                            ->get();

                        $options = [];

                        $locations->each(function($location) use (&$options) {
                            $options[$location->id] = $location->name;
                        });

                        return $options;
                    }),
                SelectFilter::make('timeframe')
                    ->label('Timeframe')
                    ->options(function(){

                        $indicator_id = $this->getOwnerRecord()->id;

                        $timeframes = IndicatorData::where('indicator_id', $indicator_id)
                            ->selectRaw('DISTINCT timeframe')
                            ->withoutGlobalScopes()
                            ->orderBy('timeframe', 'desc')
                            ->get();

                        $options = [];

                        $timeframes->each(function($timeframe) use (&$options) {

                            $options[$timeframe->timeframe] = $timeframe->timeframe;
                        });

                        return $options;

                    }),
                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No')
                    ->attribute('is_published'),
            ])
            ->groups([
                Group::make('updated_at')
                    ->label('Updated Date'),
                Group::make('created_at')
                    ->label('Created Date')
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('set_published')
                        ->label('Publish')
                        ->action(fn($records)=> IndicatorData::whereIn('id', $records->pluck('id'))->update(['is_published' => true]))
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check-circle'),
                    BulkAction::make('set_unpublished')
                        ->label('Unpublish')
                        ->action(fn($records)=> IndicatorData::whereIn('id', $records->pluck('id'))->update(['is_published' => false]))
                        ->requiresConfirmation()
                        ->color('warning')
                        ->icon('heroicon-o-check-circle'),
                ]),
                
            ]);
    }
}
