<?php

namespace App\Http\Resources;

use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AssetResource;
use App\Http\Resources\AssetSubcategoryResource;


class AssetCategoryResource extends JsonResource
{

    use HandlesAPIRequestOptions;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $wants_geojson = $this->wantsGeoJSON($request);

        $asset_category = $this['asset_category'];

        if($wants_geojson){

            $assets = [
                'type' => 'FeatureCollection',
               'features' => AssetGeoJSONResource::collection($this['assets'])
            ];

        } else {

            $assets = AssetResource::collection($this['assets']);
        }

        return [
            'id' => $asset_category->id,
            'name' => $asset_category->name,
            'slug' => $asset_category->slug,
            'subcategories' => AssetSubcategoryResource::collection($asset_category->children),
            'assets' => $assets
        ];
    }
}
