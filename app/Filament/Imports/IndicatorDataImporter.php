<?php

namespace App\Filament\Imports;

use App\Models\IndicatorData;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Illuminate\Support\Number;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Http\UploadedFile;
use Filament\Actions\Imports\Models\Import;
use App\Models\Breakdown;


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
    

     public static function getOptionsFormComponents(): array{
            return [
                TextInput::make('row_offset')
                    ->label('CSV Row Offset')
                    ->helperText('Number of rows to skip at the start of the CSV file (e.g. for headers).')
                    ->default(0)
                    ->numeric()
                    ->required(),
                Select::make('location_bind')
                    ->label('Location Binding Value')
                    ->helperText('The value to bind the location to for each imported row.')
                    ->options([
                        'fips_geopolitical_id'  => 'FIPS/Geopolitical ID',
                        'name'  => 'Location Name',
                    ])
                    ->required()
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
            ImportColumn::make('fips/geopolitical_id')
                ->relationship('location', resolveUsing: ['fips', 'geopolitical_id'])
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
