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
                'timeframes'     => $this['data']['timeframes'] ?? [],
                'location_types' => $this['data']['location_types'] ?? [],
                'data_formats'   => $this['data']['data_formats'] ?? [],
                'breakdowns'     => $this['data']['breakdowns'] ?? [],
            ],
        ];
        
    }
}
