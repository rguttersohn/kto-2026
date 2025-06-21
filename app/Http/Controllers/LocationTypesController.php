<?php

namespace App\Http\Controllers;

use App\Support\StandardizeResponse;
use App\Models\LocationType;

class LocationTypesController extends Controller
{
    public function getLocationTypes(){


        $location_types = LocationType::select('id', 'name', 'plural_name','slug','scope', 'classification')
            ->get();

        return StandardizeResponse::internalAPIResponse(
            data: [
                'location_types' => $location_types
            ]);

    }

    public function getLocationType($location_type_slug){


        $location_type = LocationType::select('id', 'name', 'plural_name','slug','scope', 'classification')
            ->with('locations:location_type_id,name,id,fips,geopolitical_id')
            ->where('slug', $location_type_slug)
            ->firstOrFail();

        if(!$location_type){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'slug not found',
                status_code: 404
            );

        }

        return StandardizeResponse::internalAPIResponse(
            data: [
                'location_type' => $location_type
            ]
            );
        
    }
}
