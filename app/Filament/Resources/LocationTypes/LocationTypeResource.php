<?php

namespace App\Filament\Resources\LocationTypes;

use App\Filament\Resources\LocationTypes\Pages\CreateLocationType;
use App\Filament\Resources\LocationTypes\Pages\EditLocationType;
use App\Filament\Resources\LocationTypes\Pages\ListLocationTypes;
use App\Filament\Resources\LocationTypes\Schemas\LocationTypeForm;
use App\Filament\Resources\LocationTypes\Tables\LocationTypesTable;
use App\Models\Location;
use App\Models\LocationType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\LocationTypes\RelationManagers\LocationsRelationManager;
use UnitEnum;

class LocationTypeResource extends Resource
{
    protected static ?string $model = LocationType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return LocationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LocationsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocationTypes::route('/'),
            'create' => CreateLocationType::route('/create'),
            'edit' => EditLocationType::route('/{record}/edit'),
        ];
    }
}
