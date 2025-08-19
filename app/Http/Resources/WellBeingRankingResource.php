<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WellBeingRankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'domain_id' => $this->when($this->domain_id, $this->domain_id, 0),
            'year' => $this->year,
            'score' => $this->score,
            'location_id' => $this->location_id
        ];
    }
}
