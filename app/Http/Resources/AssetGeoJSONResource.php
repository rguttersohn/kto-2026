<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetGeoJSONResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'type' => 'Feature',
            'geometry' => json_decode($this->resource->location),
            'properties' => array_filter($this->resource->toArray(), fn($_d)=>$_d !== 'location', ARRAY_FILTER_USE_KEY)
        ];
    }
}
