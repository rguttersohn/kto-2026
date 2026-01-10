<?php
namespace App\Support;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GeoJSON {

    public static function getGeoJSON(array $data, string $geometry_column):array{

            return  [
                    'type' => 'FeatureCollection',
                    'features' => array_map(function($d)use($geometry_column){
                      
                        return [
                            'type' => 'Feature',
                            'geometry' => json_decode($d[$geometry_column]),
                            'properties' => array_filter($d, fn($a)=>$a !== $geometry_column, ARRAY_FILTER_USE_KEY)
                        ];

                    }, $data)
                ];
        
    }

    public static function wrapGeoJSONResource(JsonResource $resource){

        $features = $resource instanceof ResourceCollection ? $resource : [$resource];

         return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }
}