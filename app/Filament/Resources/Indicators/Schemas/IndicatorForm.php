<?php

namespace App\Filament\Resources\Indicators\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Filament\Support\UIPermissions;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use App\Filament\Services\AdminIndicatorService;
use App\Models\Breakdown;
use App\Models\Location;
use App\Models\IndicatorData;
use Filament\Schemas\Components\Grid;

class IndicatorForm
{   

    protected static function getBreakdownOptions($record){

        $indicator_id = $record->id;

        return AdminIndicatorService::rememberFilter($indicator_id, 'breakdown',

                function()use($indicator_id){
                    return Breakdown::select('breakdowns.name', 'breakdowns.id')
                        ->join('indicators.data', 'breakdowns.id', '=', 'indicators.data.breakdown_id')
                        ->where('indicators.data.indicator_id', $indicator_id)
                        ->distinct()
                        ->pluck('name', 'id');

            });
    }

    protected static function getLocationTypeOptions($record){

        $indicator_id = $record->id;

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

    protected static function getLocationOptions($get, $record){

        $indicator_id = $record->id;

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

    protected static function getTimeframeOptions($record){

        $indicator_id = $record->id;

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

    protected static function getFormatOptions($record){

        $indicator_id = $record->id;

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

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Textarea::make('definition')
                    ->columnSpanFull(),
                RichEditor::make('source')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript','link'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ]),
                RichEditor::make('note')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript','link'],
                        ['alignStart', 'alignCenter', 'alignEnd'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ]),
                TextArea::make('data_flag')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->disabled(fn()=>!UIPermissions::canPublish())
                    ->required(),
                Toggle::make('is_archived')
                    ->disabled(fn()=>!UIPermissions::canPublish())
                    ->required(),
                Section::make('SEO and Meta')
                    ->columnSpanFull()
                    ->relationship('meta')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->helperText('The title as it will appear in search results. Max Character Count of 60')
                            ->maxLength(60),
                        TextInput::make('meta_description')
                            ->label('Meta Description')
                            ->helperText('The description as it will appear in search results. Max Character count of 160')
                            ->maxLength(160),
                        TextInput::make('og_title')
                            ->label('Open Graph Title')
                            ->helperText('The title as it will appear when shared. Max Character count of 60')
                            ->maxLength(60),
                        TextInput::make('og_description')
                            ->helperText('The description as it will appear when shared. Max Character count of 160')
                            ->maxLength(160),
                    ]),
                Section::make('Default Filters')
                    ->columnSpanFull()
                    ->relationship('defaultFilters')
                    ->schema(fn($record)=>[
                        Grid::make([
                            'default' => 1,
                            'lg' => 3
                        ])
                        ->schema([
                            Select::make('timeframe')
                                ->label('Select Timeframe Filter')
                                ->options(fn()=>self::getTimeframeOptions($record))
                                ->searchable(),
                            Select::make('data_format_id')
                                ->label('Select Data Format Filter')
                                ->options(fn()=>self::getFormatOptions($record))
                                ->searchable(),
                            Select::make('breakdown_id')
                                ->label('Select Breakdown Filter')
                                ->options(fn()=>self::getBreakdownOptions($record))
                                ->searchable(),
                            Select::make('location_type_id')
                                ->label('Select Location Type Filter')
                                ->options(self::getLocationTypeOptions($record))
                                ->searchable()
                                ->live(),
                            Select::make('location_id')
                                ->label('Select Location Filter')
                                ->options(fn($get)=>self::getLocationOptions($get, $record))
                                ->searchable()
                                ->live()
                        ])
                    ])
            ]);
    }
}
