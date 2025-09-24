<?php

namespace App\Filament\Resources\Domains\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DomainForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('name')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('definition')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_rankable')
                    ->required(),
            ]);
    }
}
