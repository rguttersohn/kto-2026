<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

class LocationGeoJSONResource extends JsonResource
{

    protected function getLocationAsGeoJSON(Model $location){
        
        $location_array = $location->toArray();

        return [
            'type' => 'Feature',
            'geometry' => json_decode($location_array['geometry']),
            'properties' => array_filter($location_array, fn($location)=>$location !== 'geometry', ARRAY_FILTER_USE_KEY)
        ];
        
    }

    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->getLocationAsGeoJSON($this->resource);
    }
}
