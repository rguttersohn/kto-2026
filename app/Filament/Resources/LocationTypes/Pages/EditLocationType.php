<?php

namespace App\Filament\Resources\LocationTypes\Pages;

use App\Filament\Resources\LocationTypes\LocationTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLocationType extends EditRecord
{
    protected static string $resource = LocationTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
