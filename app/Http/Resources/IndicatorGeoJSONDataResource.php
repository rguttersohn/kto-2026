<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

class IndicatorGeoJSONDataResource extends JsonResource
{

    protected function getDataAsGeoJSON(Model $indicator){

        $indicator_array = $indicator->toArray();

        $geojson = [
                'id' => $indicator['id'],
                'name' => $indicator['name'],
                'slug' => $indicator['slug'],
                'data' => [ 'type' => 'FeatureCollection',
                            'features' => array_map(function($d){
                                return [
                                    'type' => 'Feature',
                                    'geometry' => json_decode($d['geometry']),
                                    'properties' => array_filter($d, fn($_d)=>$_d !== 'geometry', ARRAY_FILTER_USE_KEY)
                                ];
        
                            }, $indicator_array['data'])
                        ]
                    ];
        

        return $geojson;

    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return $this->getDataAsGeoJSON($this->resource);
    }
}
