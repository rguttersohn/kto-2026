<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCategoryCustomLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $asset_category = $this['asset_category'];
        $assets = $this['assets'];

        return [
            'id' => $asset_category->id,
            'name' => $asset_category->name,
            'slug' => $asset_category->slug,
            'subcategories' => AssetSubcategoryResource::collection($asset_category->children),
            'assets' => $assets->count
        ];
    }
}
