<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationIndicatorFiltersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $indicator = $this['indicator'];

        $location = $this['location'];

        return [
            'id' => $location->id,
            'name' => $location->name,
            'fips' => $location->fips,
            'geopolitical_id' => $location->geopolitical_id,
            'indicator' => new IndicatorFiltersResource($indicator)
        ];
    }
}
