<?php

namespace App\Filament\Resources\Breakdowns\Pages;

use App\Filament\Resources\Breakdowns\BreakdownResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBreakdowns extends ListRecords
{
    protected static string $resource = BreakdownResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
