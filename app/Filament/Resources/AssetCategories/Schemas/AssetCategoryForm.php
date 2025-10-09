<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\AssetCategory;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('name')
                    ->required()
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->options(fn()=>AssetCategory::whereNull('parent_id')->get()->pluck('name', 'id')),
            ]);
    }
}
