<?php

namespace App\Filament\Resources\IndicatorCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndicatorCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('domain_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
