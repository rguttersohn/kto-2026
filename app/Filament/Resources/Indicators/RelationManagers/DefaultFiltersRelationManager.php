<?php

namespace App\Filament\Resources\Indicators\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Enums\IndicatorFilterTypes;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use App\Filament\Services\AdminIndicatorService;
use App\Models\Breakdown;
use App\Models\Location;
use App\Models\IndicatorData;

class DefaultFiltersRelationManager extends RelationManager
{
    protected static string $relationship = 'defaultFilters';

    protected function getBreakdownOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        return AdminIndicatorService::rememberFilter($indicator_id, 'breakdowns',

                function()use($indicator_id){

                    return Breakdown::select('breakdowns.name', 'breakdowns.id')
                        ->join('indicators.data', 'breakdowns.id', '=', 'indicators.data.breakdown_id')
                        ->where('indicators.data.indicator_id', $indicator_id)
                        ->distinct()
                        ->pluck('name', 'id');

            });
    }

    protected function getLocationTypeOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        return AdminIndicatorService::rememberFilter($indicator_id, 'location_types', 

                function()use($indicator_id){
                    
                    return Location::select('locations.location_types.name', 'locations.location_types.id')
                        ->join('indicators.data', 'indicators.data.location_id', 'locations.locations.id')
                        ->join('locations.location_types', 'locations.locations.location_type_id', 'locations.location_types.id')
                        ->where('indicators.data.indicator_id', $indicator_id)
                        ->distinct()
                        ->pluck('name','id');
                }
        );

    }

    protected function getLocationOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        return AdminIndicatorService::rememberFilter($indicator_id, 'locations', 

                function()use($indicator_id){
                    
                    return Location::select('locations.locations.name', 'locations.locations.id')
                        ->join('indicators.data', 'indicators.data.location_id', 'locations.locations.id')
                        ->where('indicators.data.indicator_id', $indicator_id)
                        ->distinct()
                        ->pluck('name','id');
                }
        );
    }

    protected function getTimeframeOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        $filters = AdminIndicatorService::rememberFilter($indicator_id, 'timeframes',
            
            function()use($indicator_id){

                return IndicatorData::where('indicator_id', $indicator_id)
                    ->select('timeframe')
                    ->distinct()
                    ->orderBy('timeframe', 'desc')
                    ->pluck('timeframe','timeframe');

            });

        return $filters;
    }

    protected function getFormatOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        $filter = AdminIndicatorService::rememberFilter($indicator_id, 'formats',

            function()use($indicator_id){

                return IndicatorData::select('indicators.data_formats.id as id', 'indicators.data_formats.name as name')
                    ->where('indicator_id', $indicator_id)
                    ->join('indicators.data_formats', 'indicators.data.data_format_id', 'indicators.data_formats.id')
                    ->distinct()
                    ->pluck('name', 'id');
            }
        );

        return $filter;

    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('filter_type')
                    ->options(
                        collect(IndicatorFilterTypes::cases())
                            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
                )->live(),
                Select::make('default_value_id')
                    ->label('Default Value')
                    ->options(function(Get $get){
                        
                        $filterType = $get('filter_type');
                        
                        return match($filterType){
                            'breakdown' => $this->getBreakdownOptions(),
                            'location_type' => $this->getLocationTypeOptions(),
                            'location' => $this->getLocationOptions(),
                            'timeframe' => $this->getTimeframeOptions(),
                            'format' => $this->getFormatOptions(),
                            default => [],
                        };

                    })
                    ->hidden(fn(Get $get):bool=>empty($get('filter_type')))
                    ->live()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Default Filters')
            ->columns([
                TextColumn::make('filter_type')
                    ->searchable()
                    ->formatStateUsing(function($state){
                        return $state?->label();
                }),
                TextColumn::make('default_value_id')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
