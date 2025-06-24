<?php

namespace App\Http\Controllers;

use App\Support\StandardizeResponse;
use App\Models\LocationType;
use App\Http\Resources\LocationTypeResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use Illuminate\Http\Request;
use App\Support\PostGIS;

class LocationTypesController extends Controller
{

    use HandlesAPIRequestOptions;

    public function getLocationType(Request $request, $location_type_id){


        $wants_geojson = $this->wantsGeoJSON($request);

        $location_type = LocationType::select('id', 'name', 'plural_name','scope', 'classification')
            ->with(['locations' => function($query)use($wants_geojson){
                $query->select('location_type_id', 'name','locations.id','fips','geopolitical_id')
                    ->when($wants_geojson, function($query){
                        $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                            ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'));
                    });
            }])
            ->where('id', $location_type_id)
            ->first();

        if(!$location_type){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 404
            );

        }

        return StandardizeResponse::internalAPIResponse(
             data: new LocationTypeResource($location_type)
            );
        
    }
}
