<?php

namespace App\Filament\Resources\DataFormats\Pages;

use App\Filament\Resources\DataFormats\DataFormatResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDataFormat extends EditRecord
{
    protected static string $resource = DataFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
