<?php

namespace App\Filament\Exports;

use App\Models\FailedImport;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class FailedImportExporter extends Exporter
{
    protected static ?string $model = FailedImport::class;

    public static function getColumns(): array
    {   

        $firstRecord = static::getModel()::query()->first();
        
        $headers = array_keys($firstRecord->data);

        $exports = [];

        foreach($headers as $header){

            $exports[] = ExportColumn::make($header)
                            ->formatStateUsing(fn($record)=>isset($record->data[$header]) ? $record->data[$header] : null);

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
