<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LocationResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\LocationGeoJSONResource;

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

        if($wants_geojson){

            $locations = [
                'type' => 'FeatureCollection',
                'features' => LocationGeoJSONResource::collection($this->whenLoaded('locations'))
            ];

        } else {
            
            $locations = LocationResource::collection($this->whenLoaded('locations'));
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'plural_name' => $this->plural_name,
            'slug' => $this->slug,
            'scope' => $this->scope,
            'classification' => $this->classification,
            'locations' => $locations
        ];
    }
}
