<?php

namespace App\Filament\Resources\LocationTypes\Schemas;

use App\Enums\LocationScopes;
use App\Enums\LocationTypeClassification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LocationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('name')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('plural_name')
                    ->required()
                    ->columnSpanFull(),
                Select::make('classification')
                    ->options(LocationTypeClassification::class)
                    ->required(),
                Select::make('scope')
                    ->options(LocationScopes::class)
                    ->default('local')
                    ->required(),
                Toggle::make('is_rankable')
                    ->required(),
            ]);
    }
}
