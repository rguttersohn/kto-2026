<?php

namespace App\Filament\Resources\WellBeingScores\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;

class WellBeingScoreForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('domain_id')
                    ->required()
                    ->numeric(),
                TextInput::make('timeframe')
                    ->required()
                    ->numeric(),
                TextInput::make('score')
                    ->required()
                    ->numeric(),
                Select::make('location_id')
                    ->relationship('location', 'name')
                    ->required(),
                Toggle::make('is_published')
                    ->required()
                    ->disabled(fn()=>!Auth::user()->isAdmin())

                ]);
    }
}
