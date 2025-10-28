<?php

namespace App\Filament\Resources\WellBeingScores;

use App\Filament\Resources\WellBeingScores\Pages\CreateWellBeingScore;
use App\Filament\Resources\WellBeingScores\Pages\EditWellBeingScore;
use App\Filament\Resources\WellBeingScores\Pages\ListWellBeingScores;
use App\Filament\Resources\WellBeingScores\Schemas\WellBeingScoreForm;
use App\Filament\Resources\WellBeingScores\Tables\WellBeingScoresTable;
use App\Models\WellBeingScore;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class WellBeingScoreResource extends Resource
{
    protected static ?string $model = WellBeingScore::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Well-Being Index';

    public static function form(Schema $schema): Schema
    {
        return WellBeingScoreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WellBeingScoresTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWellBeingScores::route('/'),
            'create' => CreateWellBeingScore::route('/create'),
            'edit' => EditWellBeingScore::route('/{record}/edit'),
        ];
    }
}
