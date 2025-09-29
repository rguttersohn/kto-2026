<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicatorResource;

class LocationIndicatorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        $indicator = $this->resource->indicators->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'fips' => $this->fips,
            'district_id' => $this->district_id,
            'indicator' => new IndicatorResource($indicator)
        ];
    }
}
