<?php

namespace App\Filament\Resources\AssetCategories\Pages;

use App\Filament\Resources\AssetCategories\AssetCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;

class ListAssetCategories extends ListRecords
{
    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs():array{

        return [
            'categories' => Tab::make('Parent Categories')
                ->modifyQueryUsing(fn(Builder $query)=>$query->whereNull('parent_id')),
            'subcategories' => Tab::make('Child Categories')
                ->modifyQueryUsing(fn(Builder $query)=>$query->whereNotNull('parent_id'))
        ];
    }
}
