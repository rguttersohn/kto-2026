<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WellBeingRankingResource;

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
            'assets' => $this->when(isset($this->assets), $this->assets),
            'rank' => $this->when(isset($this->rank), $this->rank),
            'rankings' => $this->when($this->relationLoaded('rankings'), WellBeingRankingResource::collection($this->rankings))
        ];
    }
}
