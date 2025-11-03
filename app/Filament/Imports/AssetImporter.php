<?php

namespace App\Filament\Imports;

use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use App\Support\PostGIS;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use App\Models\AssetSchema;
use App\Filament\Support\AssetSchemaValidation;
use Exception;
use Illuminate\Support\Facades\Log;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    protected AssetSchema | null $schema = null;

    protected function getAssetSchema():AssetSchema | null{

        if($this->schema){

            return $this->schema;
       
        }

        return AssetSchema::where('asset_category_id', $this->options['asset_category_id'])->first();

    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('longitude'),
            ImportColumn::make('latitude')   
        ];
    }

    public function resolveRecord(): Asset
    {   
        
        $asset = new Asset();

        $asset_category_id = $this->options['asset_category_id'];

        $asset->asset_category_id = $asset_category_id;

        $asset->import_id = $this->import->getKey();

        $data_array = [];

        foreach($this->data as $header=>$value){
            
            if(str_starts_with($header, 'data:')){

                $key = str_replace('data:', '', $header);
                $data_array[$key] = $value;

            }

        }

        if(!$data_array){

            throw new RowImportFailedException("No data columns provided for asset.");
            
        }

        if($this->getAssetSchema()){

            $validation = AssetSchemaValidation::validateData($this->getAssetSchema(), $data_array);

            if($validation instanceof Exception){

                throw new RowImportFailedException($validation->getMessage());
            }
            
        }

        $asset->data = $data_array; 

        return $asset;

    }

    protected function beforeSave(): void
    {   

        $this->record->geometry = PostGIS::createPoint([$this->data['longitude'], $this->data['latitude']]);
        
        unset($this->record['longitude']);
        unset($this->record['latitude']);

    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your asset import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
