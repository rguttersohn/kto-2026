<?php

namespace App\Filament\Imports;

use App\Models\IndicatorData;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Illuminate\Support\Number;
use Filament\Forms\Components\Toggle;
use Illuminate\Http\UploadedFile;
use Filament\Actions\Imports\Models\Import;
use App\Models\Breakdown;
use App\Models\Location;
use Illuminate\Support\Facades\Log;

class IndicatorDataImporter extends Importer
{
    protected static ?string $model = IndicatorData::class;

    protected int $all_breakdown_id; 

    protected function normalizeCSVFile(UploadedFile $file): string
    {
        $contents = file_get_contents($file->getRealPath());

        $is_utf16 = substr($contents, 0, 2) === "\xFF\xFE";

        if ($is_utf16) {
            
            $contents = mb_convert_encoding($contents, 'UTF-8', 'UTF-16LE');
        
        }

        return $contents;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Toggle::make('use_legacy_district_id')
                ->label('Match Location With Legacy District ID')
                ->helperText('If enabled, the importer will match locations using FIPS from the original Keeping Track Database.')
                ->default(false)
                ->columnSpanFull(),
                ];
    }
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('data')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),
            ImportColumn::make('dataFormat')
                ->relationship('dataFormat', resolveUsing: 'name')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('timeframe')
                ->requiredMapping()
                ->rules(['required', 'integer']),
            ImportColumn::make('fips/district_id')
                ->relationship('location', resolveUsing: function($data, $options){
                    
                    $use_legacy_district_id = $options['use_legacy_district_id'];
                    
                    $location = Location::query()
                        ->when($use_legacy_district_id, function ($query) use ($data) {
                            
                            $query->where('locations.locations.legacy_district_id', $data['fips/district_id']);

                        })
                        ->when($use_legacy_district_id === false, function ($query) use ($data) {
                            
                            $query->where('locations.locations.fips', $data['fips/district_id'])
                                  ->orWhere('locations.locations.district_id', $data['fips/district_id']);

                        })
                        ->first();
                    
                    return $location;
                })
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('breakdown')
                ->relationship('breakdown', resolveUsing: 'name')
                ->rules(['string']),
        ];
    }


    public function resolveRecord(): IndicatorData
    {

        $indicator_data = new IndicatorData();

        $indicator_data->indicator_id = $this->options['indicator_id'];
        
        if(!isset($this->data['breakdown'])){
            
            if(!isset($this->all_breakdown_id)){
                $this->all_breakdown_id = Breakdown::where('name', 'All')->first()->id;
            }
            
            $indicator_data->breakdown_id = $this->all_breakdown_id;
            
        }

        return $indicator_data;

    }

    

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your indicator data import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

}
