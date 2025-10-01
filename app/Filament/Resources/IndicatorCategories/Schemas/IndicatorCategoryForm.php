<?php

namespace App\Filament\Resources\IndicatorCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;

class IndicatorCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('domain')
                    ->relationship('domain', 'name')
                    ->required(),
            ]);
    }
}
