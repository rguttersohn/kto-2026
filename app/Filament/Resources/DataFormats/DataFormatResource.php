<?php

namespace App\Filament\Resources\DataFormats;

use App\Filament\Resources\DataFormats\Pages\CreateDataFormat;
use App\Filament\Resources\DataFormats\Pages\EditDataFormat;
use App\Filament\Resources\DataFormats\Pages\ListDataFormats;
use App\Filament\Resources\DataFormats\Schemas\DataFormatForm;
use App\Filament\Resources\DataFormats\Tables\DataFormatsTable;
use App\Models\DataFormat;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DataFormatResource extends Resource
{
    protected static ?string $model = DataFormat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Indicators';

    public static function form(Schema $schema): Schema
    {
        return DataFormatForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataFormatsTable::configure($table);
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
            'index' => ListDataFormats::route('/'),
            'create' => CreateDataFormat::route('/create'),
            'edit' => EditDataFormat::route('/{record}/edit'),
        ];
    }
}
