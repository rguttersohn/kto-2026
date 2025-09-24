<?php

namespace App\Filament\Resources\DataFormats\Pages;

use App\Filament\Resources\DataFormats\DataFormatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataFormats extends ListRecords
{
    protected static string $resource = DataFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
