<?php

namespace App\Filament\Imports;

use App\Models\WellBeingScore;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Domain;

class WellBeingScoreImporter extends Importer
{
    protected static ?string $model = WellBeingScore::class;


    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('domain')
                ->label('Select Domain')
                ->options(fn()=>Domain::where('is_rankable', true)->get()->pluck('name', 'id')),
            TextInput::make('timeframe')
                ->numeric()
                ->minValue(2000)
        ];
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('score')
                ->label('Index Score')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),
            ImportColumn::make('location')
                ->label('FIPS/District ID')
                ->relationship(resolveUsing:['fips', 'district_id'])
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): WellBeingScore
    {
        $well_being = new WellBeingScore();

        $well_being->domain_id = $this->options['domain'];

        $well_being->timeframe = $this->options['timeframe'];

        return $well_being;
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
