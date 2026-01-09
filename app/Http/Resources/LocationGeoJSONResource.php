<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LocationResource;

class LocationGeoJSONResource extends JsonResource
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
            'geometry' => json_decode($this->geometry),
            'properties' => new LocationResource($this->resource)
            
        ];
    }
}
