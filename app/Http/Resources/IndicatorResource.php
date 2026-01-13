<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicatorDataResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Support\GeoJSON;
use App\Http\Resources\IndicatorBreakdownResource;

class IndicatorResource extends JsonResource
{
    use HandlesAPIRequestOptions;

    public function toArray($request){
        
        return [
            "id" => $this->id,
            "name" => $this->name,
            "definition" => $this->definition,
            "source" => $this->source,
            "note" => $this->note,
            "data" => $this->whenLoaded('data', function () use ($request) {
                
                $wants_geojson = $this->wantsGeoJSON($request);
        
                return $wants_geojson
                    ? GeoJSON::wrapGeoJSONResource(IndicatorGeoJSONDataResource::collection($this->data))
                    : IndicatorDataResource::collection($this->data);

            }),
            'filters' => $this->whenLoaded('filters', fn()=>[
                'format' => IndicatorFormatResource::collection($this->filters['format']),
                'location_type' => LocationTypeResource::collection($this->filters['location_type']),
                'breakdown' => IndicatorBreakdownResource::collection($this->filters['breakdown']),
                'timeframe' => $this->filters['timeframe']->toArray()
            ])
        ];
        
    }
}
