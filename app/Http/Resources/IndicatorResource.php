<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\IndicatorDataResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Support\GeoJSON;


class IndicatorResource extends JsonResource
{
    use HandlesAPIRequestOptions;

    public function toArray($request){

        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "definition" => $this->definition,
            "source" => $this->source,
            "note" => $this->note,
            "data" => $this->whenLoaded('data', function () use ($request) {
                $wants_geojson = $this->wantsGeoJSON($request);
        
                return $wants_geojson
                    ? GeoJSON::wrapGeoJSONResource(IndicatorGeoJSONDataResource::collection($this->data))
                    : IndicatorDataResource::collection($this->data);
            }),
        ];
        
    }
}
