<?php

namespace App\Filament\Resources\Indicators\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Filament\Support\UIPermissions;

class IndicatorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('category')
                    ->relationship('category', 'name')
                    ->required(),
                Textarea::make('definition')
                    ->columnSpanFull(),
                Textarea::make('source')
                    ->columnSpanFull(),
                Textarea::make('note')
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->disabled(fn()=>!UIPermissions::canPublish())
                    ->required(),
            ]);
    }
}
