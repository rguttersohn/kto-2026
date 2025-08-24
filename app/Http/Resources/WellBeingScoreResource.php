<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WellBeingScoreResource extends JsonResource
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
            'rank' => $this->rank,
            'location_id' => $this->location_id,
            'location_name' => $this->when(isset($this->name), $this->name),
            
        ];
    }
}
