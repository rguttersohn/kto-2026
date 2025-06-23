<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

class IndicatorGeoJSONDataResource extends JsonResource
{

    protected function getDataAsGeoJSON(Model $indicator){

        return [
            'type' => 'Feature',
            'geometry' => json_decode($this->geometry),
            'properties' => array_filter($this->resource->toArray(), fn($resource)=>$resource !== 'geometry', ARRAY_FILTER_USE_KEY)
        ];
        
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return $this->getDataAsGeoJSON($this->resource);
    }
}
