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
                "location_name" => $this->location_name,
                "location_type_id" => $this->location_type_id,
                "location_type_name" => $this->location_type_name,
                "timeframe" => $this->timeframe,
                "breakdown_parent_name" => $this->breakdown_parent_name,
                "breakdown_parent_id" => $this->breakdown_parent_id,
                "breakdown_name" => $this->breakdown_name,
                'breakdown_id' => $this->breakdown_id,
                "format_name" => $this->format_name,
                'format_id' => $this->format_id
            ];
        
    }
}
