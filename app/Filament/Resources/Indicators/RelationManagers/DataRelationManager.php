<?php

namespace App\Filament\Resources\Indicators\RelationManagers;

use App\Filament\Imports\IndicatorDataImporter;
use App\Filament\Support\UIPermissions;
use App\Models\Breakdown;
use App\Models\DataFormat;
use Filament\Actions\ImportAction;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use App\Models\IndicatorData;
use Filament\Actions\BulkAction;
use Filament\Tables\Grouping\Group;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Models\Import;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Location;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataRelationManager extends RelationManager
{
    protected static string $relationship = 'data';

    public function form(Schema $schema):Schema {

        return $schema->components([
            TextInput::make('data')
                ->numeric()
                ->required(),
            Select::make('data_format_id')
                ->label('Data Format')
                ->required()
                ->options(fn()=>DataFormat::all()->pluck('name', 'id'))
                ->searchable(),
            TextInput::make('timeframe')
                ->numeric()
                ->required()
                ->minValue(1900),
            Select::make('location_id')
                ->label('Location')
                ->required()
                ->options(fn()=>Location::all()->pluck('name', 'id'))
                ->searchable(),
            Select::make('breakdown_id')
                ->label('Breakdown')
                ->required()
                ->options(fn()=>Breakdown::all()->pluck('name', 'id')),
            Toggle::make('is_published')
                ->required()
                ->columnSpanFull()
                ->disabled(fn()=>!UIPermissions::canPublish())
        ]);
    }
       
    public function table(Table $table): Table
    {

        DB::listen(function ($query) {
            Log::info('Query on indicator edit', [
                'sql' => $query->sql,
                'time' => $query->time . 'ms',
                'bindings' => $query->bindings
            ]);
        });

        return $table
            ->columns([
                TextColumn::make('data')
                    ->numeric(),
                TextColumn::make('dataFormat.name'),
                TextColumn::make('timeframe'),
                TextColumn::make('location.locationType.name')
                    ->label('Location Type'),
                TextColumn::make('location.name'),
                TextColumn::make('breakdown.name'),
                IconColumn::make('is_published')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('import_id.file_path')
                    ->label('Import Group')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->label('Breakdown')
                    ->searchable()
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
                    ->searchable()
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
                    ->searchable()
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
                SelectFilter::make('import_id')
                    ->label('Import Group')
                    ->searchable()
                    ->options(function(){

                        $indicator_id = $this->getOwnerRecord()->id;


                        $import_ids = IndicatorData::where('indicator_id', $indicator_id)
                            ->selectRaw('DISTINCT import_id')
                            ->withoutGlobalScopes()
                            ->get();
                        
                        return Import::select('file_name', 'id')
                            ->where('importer', 'App\Filament\Imports\IndicatorDataImporter')
                            ->whereIn('id', $import_ids->pluck('import_id')->toArray())
                            ->get()
                            ->pluck('file_name', 'id')
                            ->toArray();
                        
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
                    ->label('Created Date'),
                Group::make('import.file_name')
                    ->label('Import Group')
                    ->getTitleFromRecordUsing(fn ($record): string => "{$record->import->file_name}_{$record->import->created_at}")
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
                
            ]);
    }
}
