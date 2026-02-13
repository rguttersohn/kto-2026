<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicatorResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {        
    
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fips' => $this->fips,
            'district_id' => $this->district_id,
            'location_type_id' => $this->location_type_id,
            'indicators' => $this->whenLoaded('indicators', fn()=> IndicatorResource::collection($this->indicators)),
            'is_uninhabited' => $this->is_uninhabited
        ];
    }
}
