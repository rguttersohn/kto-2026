<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;

class IndicatorDataResource extends JsonResource
{
    
    public static function getDataAsGeoJSON(Model $indicator){

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
        if($this->resource instanceof Model){

            return [

                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'data' => $this->data

            ];

        }

        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'slug' => $this['slug'],
            'data' => $this['data']
        ];
    }
}
