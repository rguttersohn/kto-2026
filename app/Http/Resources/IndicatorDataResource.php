<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndicatorDataResource extends JsonResource
{
    
    
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
          
            return [
                "id" => $this->id,
                "data" => $this->data,
                "indicator_id" => $this->indicator_id,
                "location_id" => $this->location_id,
                "location" => $this->location,
                "location_type_id" => $this->location_type_id,
                "location_type" => $this->location_type,
                "timeframe" => $this->timeframe,
                "breakdown" => $this->breakdown_name,
                "format" => $this->format
            ];
        
    }
}
