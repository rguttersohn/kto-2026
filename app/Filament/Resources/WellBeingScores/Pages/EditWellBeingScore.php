<?php

namespace App\Filament\Resources\WellBeingScores\Pages;

use App\Filament\Resources\WellBeingScores\WellBeingScoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWellBeingScore extends EditRecord
{
    protected static string $resource = WellBeingScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
