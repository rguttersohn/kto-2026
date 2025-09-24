<?php

namespace App\Filament\Resources\WellBeingScores\Pages;

use App\Filament\Resources\WellBeingScores\WellBeingScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWellBeingScores extends ListRecords
{
    protected static string $resource = WellBeingScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
