<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicatorDataResource;

class LocationIndicatorDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $location = $this['location'];

        $indicator = $this['indicator'];


        return [
            'id' => $location->id,
            'name' => $location->name,
            'fips' => $location->fips,
            'geopolitical_id' => $location->geopolitical_id,
            'indicator' => new IndicatorDataResource($indicator)
        ];
    }
}
