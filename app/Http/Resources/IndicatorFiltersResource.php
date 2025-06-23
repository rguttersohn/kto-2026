<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class IndicatorFiltersResource extends JsonResource
{   

    public function toArray($request)
    {

        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'slug' => $this['slug'],
            'filters' => [
                'timeframe'     => $this['data']['timeframe'] ?? [],
                'location_type' => $this['data']['location_type'] ?? [],
                'format'   => $this['data']['format'] ?? [],
                'breakdown'     => $this['data']['breakdown'] ?? [],
            ],
        ];
        
    }
}
