<?php

namespace App\Filament\Resources\AssetCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use App\Models\AssetCategory;
use Filament\Forms\Components\Toggle;
use App\Filament\Support\UIPermissions;

class AssetCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('parent_id')
                    ->label('Assign Parent')
                    ->helperText('Assigning a parent will turn a category into a subcategory')
                    ->options(fn()=>AssetCategory::whereNull('parent_id')->get()->pluck('name', 'id')->prepend('None', null)),
                Toggle::make('is_published')
                    ->required()
                    ->disabled(fn($state)=>!UIPermissions::canPublish($state))
            ]);
    }
}
