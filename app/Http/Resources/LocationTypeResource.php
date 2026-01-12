<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LocationResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\LocationGeoJSONResource;
use App\Support\GeoJSON;


class LocationTypeResource extends JsonResource
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

        $locations = $this->whenLoaded('locations', function() use ($wants_geojson) {
            
            if($wants_geojson){
                
                return GeoJSON::wrapGeoJSONResource(LocationGeoJSONResource::collection($this->locations));

            }

            return LocationResource::collection($this->locations);
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'plural_name' => $this->plural_name,
            'scope' => $this->scope,
            'classification' => $this->classification,
            'locations' => $locations,
            'indicators' => $this->whenLoaded('indicators', fn()=>IndicatorResource::collection($this->indicators))
        ];

    }
}
