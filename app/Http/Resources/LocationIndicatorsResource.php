<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicatorsResource;

class LocationIndicatorsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $indicators = $this->resource->data;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'fips' => $this->fips,
            'geopolitical_id' => $this->geopolitical_id,
            'indicator' => IndicatorsResource::collection($indicators)
        ];
    }
}
