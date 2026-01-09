<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

class IndicatorGeoJSONDataResource extends JsonResource
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
            'properties' => new IndicatorDataResource($this->resource)
        ];
    }
}
