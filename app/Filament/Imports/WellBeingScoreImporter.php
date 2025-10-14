<?php

namespace App\Filament\Imports;

use App\Models\WellBeingScore;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class WellBeingScoreImporter extends Importer
{
    protected static ?string $model = WellBeingScore::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('domain')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('timeframe')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('score')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('location')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): WellBeingScore
    {
        return new WellBeingScore();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your well being score import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
