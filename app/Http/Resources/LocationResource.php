<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WellBeingScoreResource;

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
            'geopolitical_id' => $this->geopolitical_id,
            'assets' => $this->when($this->relationLoaded('assets'), fn()=>$this->assets),
            'rank' => $this->when(isset($this->rank), $this->rank),
            'rankings' => $this->when($this->relationLoaded('wellBeingScores'), fn()=>WellBeingScoreResource::collection($this->rankings))
        ];
    }
}
