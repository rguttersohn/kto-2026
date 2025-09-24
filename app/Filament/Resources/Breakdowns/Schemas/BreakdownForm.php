<?php

namespace App\Filament\Resources\Breakdowns\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BreakdownForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('parent_id')
                    ->numeric(),
            ]);
    }
}
