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
        $indicators = $this->resource->indicators;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'fips' => $this->fips,
            'district_id' => $this->district_id,
            'indicators' => IndicatorsResource::collection($indicators)
        ];
    }
}
