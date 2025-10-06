<?php

namespace App\Filament\Exports;

use App\Models\FailedImport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

use function Illuminate\Log\log;

class FailedImportExporter extends Exporter
{
    protected static ?string $model = FailedImport::class;

    public static function getColumns(): array
    {   

        $failed_import_class = static::getModel();
        
        $failed_import = $failed_import_class::where('import_id', 8)->first();

        $headers = array_keys($failed_import->data);

        $exports = [];


        foreach($headers as $header){

            $exports[] = ExportColumn::make($header)
                            ->formatStateUsing(fn($record)=>$record->data[$header]);

        }

        $exports[] = ExportColumn::make('validation_error');


        return $exports;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your failed import export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
