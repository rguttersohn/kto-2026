<?php
namespace App\Support;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GeoJSON {

    public static function wrapGeoJSONResource(JsonResource $resource){

        $features = $resource instanceof ResourceCollection ? $resource : [$resource];

         return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }
}