<?php

namespace App\Filament\Resources\IndicatorCategories;

use App\Filament\Resources\IndicatorCategories\Pages\CreateIndicatorCategory;
use App\Filament\Resources\IndicatorCategories\Pages\EditIndicatorCategory;
use App\Filament\Resources\IndicatorCategories\Pages\ListIndicatorCategories;
use App\Filament\Resources\IndicatorCategories\Schemas\IndicatorCategoryForm;
use App\Filament\Resources\IndicatorCategories\Tables\IndicatorCategoriesTable;
use App\Models\IndicatorCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IndicatorCategoryResource extends Resource
{
    protected static ?string $model = IndicatorCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Indicators';

    public static function form(Schema $schema): Schema
    {
        return IndicatorCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorCategoriesTable::configure($table);
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
            'index' => ListIndicatorCategories::route('/'),
            'create' => CreateIndicatorCategory::route('/create'),
            'edit' => EditIndicatorCategory::route('/{record}/edit'),
        ];
    }
}
