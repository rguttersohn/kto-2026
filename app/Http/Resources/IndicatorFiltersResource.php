<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class IndicatorFiltersResource extends JsonResource
{   

    public function toArray($request)
    {   

        return [
            
            'timeframe' => $this['timeframe'] ?? [],
            'location_type' => $this['location_type'] ?? [],
            'format'=> $this['format'] ?? [],
            'breakdown'=> $this['breakdown'] ?? [],
        ];
        
    }
}
