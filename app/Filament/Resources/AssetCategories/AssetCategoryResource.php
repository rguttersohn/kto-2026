<?php

namespace App\Filament\Resources\AssetCategories;

use App\Filament\Resources\AssetCategories\Pages\CreateAssetCategory;
use App\Filament\Resources\AssetCategories\Pages\EditAssetCategory;
use App\Filament\Resources\AssetCategories\Pages\ListAssetCategories;
use App\Filament\Resources\AssetCategories\Schemas\AssetCategoryForm;
use App\Filament\Resources\AssetCategories\Tables\AssetCategoriesTable;
use App\Models\AssetCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use App\Filament\Resources\AssetCategories\RelationManagers\AssetsRelationManager;
use App\Filament\Resources\AssetCategories\RelationManagers\ChildrenRelationManager;
use Illuminate\Database\Eloquent\Builder;

class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Community Assets';

    protected static ?int $navigationSort = 98;

    public static function getEloquentQuery():Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Schema $schema): Schema
    {
        return AssetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ChildrenRelationManager::class,
            AssetsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'edit' => EditAssetCategory::route('/{record}/edit'),
        ];
    }

    
}
