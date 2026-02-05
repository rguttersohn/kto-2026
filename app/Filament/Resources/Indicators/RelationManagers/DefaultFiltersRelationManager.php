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
use Filament\Forms\Components\Select;
use App\Filament\Services\AdminIndicatorService;
use App\Models\Breakdown;
use App\Models\Location;
use App\Models\IndicatorData;
use Filament\Schemas\Components\Utilities\Get;


class DefaultFiltersRelationManager extends RelationManager
{
    protected static string $relationship = 'defaultFilters';

    protected function getBreakdownOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        return AdminIndicatorService::rememberFilter($indicator_id, 'breakdown',

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

        return AdminIndicatorService::rememberFilter($indicator_id, 'location_type', 

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

    protected function getLocationOptions( $get){

        $indicator_id = $this->getOwnerRecord()->id;

        $location_type_id = $get('location_type_id');

        return AdminIndicatorService::rememberFilter($indicator_id, "location", 

                function()use($indicator_id, $location_type_id){
                    
                    return Location::select('locations.locations.name', 'locations.locations.id')
                        ->join('indicators.data', 'indicators.data.location_id', 'locations.locations.id')
                        ->where('indicators.data.indicator_id', $indicator_id)
                        ->when($location_type_id, function($query)use($location_type_id){

                            return $query->where('location_type_id', $location_type_id);
                        })
                        ->distinct()
                        ->pluck('name','id');
                },
                "$location_type_id"
        );
    }

    protected function getTimeframeOptions(){

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
    }

    protected function getFormatOptions(){

        $indicator_id = $this->getOwnerRecord()->id;

        $filter = AdminIndicatorService::rememberFilter($indicator_id, 'format',

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
                Select::make('timeframe')
                    ->label('Select Timeframe Filter')
                    ->options($this->getTimeframeOptions()),
                Select::make('data_format_id')
                    ->label('Select Data Format Filter')
                    ->options($this->getFormatOptions()),
                Select::make('breakdown_id')
                    ->label('Select Breakdown Filter')
                    ->options($this->getBreakdownOptions()),
                Select::make('location_type_id')
                    ->label('Select Location Type Filter')
                    ->options($this->getLocationTypeOptions())
                    ->live(),
                Select::make('location_id')
                    ->label('Select Location Filter')
                    ->options(fn(Get $get)=>$this->getLocationOptions($get))
                    ->live()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Default Filters')
            ->columns([
                TextColumn::make('timeframe')
                    ->label('Timeframe Filter'),
                TextColumn::make('formatFilter.name')
                    ->label('Format Filter'),
                TextColumn::make('breakdownFilter.name')
                    ->label('Breakdown Filter'),
                TextColumn::make('locationTypeFilter.name')
                    ->label('Location Type Filter'),
                TextColumn::make('locationFilter.name')
                    ->label('Location Filter'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn()=>!$this->getOwnerRecord()->defaultFilters)
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
