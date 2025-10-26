<?php

namespace App\Filament\Resources\Breakdowns\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\Breakdown;

class BreakdownForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('parent_id')
                    ->options(fn()=>Breakdown::whereNull('parent_id')->get()->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}
