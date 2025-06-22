<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AssetSubcategoryResource;

class AssetCategoryLocationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $asset_category = $this['asset_category'];

        return [
            'id' => $asset_category->id,
            'name' => $asset_category->name,
            'slug' => $asset_category->slug,
            'subcategories' => AssetSubcategoryResource::collection($asset_category->children),
            'location_type' => new LocationTypeResource($this['location_type'])
        ];
    }
}
