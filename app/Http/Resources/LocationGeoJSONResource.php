<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

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
                [
                'type' => 'Feature',
                'geometry' => json_decode($this->geometry),
                'properties' => [
                    'id' => $this->id,
                    'name' => $this->name,
                    'fips' => $this->fips,
                    'geopolitical_id' => $this->geopolitical_id,
                ]
            ]
        ];
    }
}
