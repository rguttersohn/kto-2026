<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AssetCountResource;

class AssetsByLocationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {   

        return [
            'location_name' => $this->resource['name'],
            'location_id' => $this->resource['id'],
            'count' => new AssetCountResource($this->resource['count']), 
        ];
    }
}
