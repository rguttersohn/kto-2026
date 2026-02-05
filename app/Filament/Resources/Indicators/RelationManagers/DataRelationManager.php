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
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Location;
use App\Filament\Services\AdminIndicatorService;

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
        
        return $table
            ->modifyQueryUsing(fn(Builder $query)=>$query
                ->select('indicators.data.*')
                ->addSelect('locations.locations.name as location_name')
                ->addSelect('locations.location_types.name as location_type_name')
                ->addSelect('indicators.data_formats.name as data_format')
                ->addSelect('indicators.breakdowns.name as breakdown')
                ->join('locations.locations', 'locations.locations.id', 'location_id' )
                ->join('locations.location_types', 'locations.locations.location_type_id', 'locations.location_types.id')
                ->join('indicators.data_formats', 'indicators.data_formats.id', 'indicators.data.data_format_id')
                ->join('indicators.breakdowns', 'indicators.breakdowns.id', 'indicators.data.breakdown_id')
                )
            ->columns([
                TextColumn::make('data')
                    ->numeric(),
                TextColumn::make('data_format'),
                TextColumn::make('timeframe'),
                TextColumn::make('location_type_name')
                    ->label('Location Type'),
                TextColumn::make('location_name'),
                TextColumn::make('breakdown'),
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
 
                        $filters = AdminIndicatorService::rememberFilter($indicator_id, 'breakdown',
            
                                function()use($indicator_id){

                                    return Breakdown::select('breakdowns.name', 'breakdowns.id')
                                        ->join('indicators.data', 'breakdowns.id', '=', 'indicators.data.breakdown_id')
                                        ->where('indicators.data.indicator_id', $indicator_id)
                                        ->distinct()
                                        ->pluck('name', 'id');

                                }
                        );

                        return $filters;
                           
                    
                    }),
                SelectFilter::make('location_id')
                    ->label('Location')
                    ->searchable()
                    ->options(function(){
                        
                        $indicator_id = $this->getOwnerRecord()->id;

                        $filters = AdminIndicatorService::rememberFilter($indicator_id, 'location', 

                                function()use($indicator_id){
                                    
                                    return Location::select('locations.locations.name', 'locations.locations.id')
                                        ->join('indicators.data', 'indicators.data.location_id', 'locations.locations.id')
                                        ->where('indicators.data.indicator_id', $indicator_id)
                                        ->distinct()
                                        ->pluck('name','id');
                                }
                        );

                        return $filters;
                    }),
                SelectFilter::make('timeframe')
                    ->label('Timeframe')
                    ->searchable()
                    ->options(function(){

                        $indicator_id = $this->getOwnerRecord()->id;

                        $filters = AdminIndicatorService::rememberFilter($indicator_id, 'timeframe',
                            
                            function()use($indicator_id){

                                return IndicatorData::where('indicator_id', $indicator_id)
                                    ->select('timeframe')
                                    ->distinct()
                                    ->orderBy('timeframe', 'desc')
                                    ->pluck('timeframe','timeframe');

                            });

                        return $filters;

                    }),
                SelectFilter::make('import_id')
                    ->label('Import Group')
                    ->searchable()
                    ->options(function(){

                        $indicator_id = $this->getOwnerRecord()->id;

                        return Import::select('file_name', 'app.imports.id')
                            ->join('indicators.data', function($join)use($indicator_id){

                                return $join
                                    ->on('indicators.data.import_id', 'app.imports.id')
                                    ->where('indicators.data.indicator_id', $indicator_id)
                                    ;
                            })
                            ->where('importer', 'App\Filament\Imports\IndicatorDataImporter')
                            ->distinct('indicators.data.import_id')
                            ->pluck('file_name', 'app.imports.id');
                        
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
                
            ])
            ->deferLoading()
            ->poll(null);


    }
}
