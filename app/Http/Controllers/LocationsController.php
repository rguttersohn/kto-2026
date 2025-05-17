<?php

namespace App\Http\Controllers;

use App\Models\LocationType;
use Illuminate\Support\Facades\Response;
use App\Models\Location;
use App\Support\StandardizeResponse;
use Illuminate\Http\Request;
use App\Models\Indicator;

class LocationsController extends Controller
{
    public function getLocationTypes(){


        $location_types = LocationType::select('id', 'name', 'plural_name','slug','scope', 'classification')
            ->get();

        return Response::json([
            'error' => [
                'status' => false,
                'message' => 'success'
            ],
            'data' => [
                'location_types' => $location_types
            ]
            ]);
    }

    public function getLocationType($location_type_slug){


        $location_type = LocationType::select('id', 'name', 'plural_name','slug','scope', 'classification')
            ->with('locations:location_type_id,name,id,fips,geopolitical_id')
            ->where('slug', $location_type_slug)
            ->firstOrFail();

        if(!$location_type){

            return Response::json([
                'error' => [
                    'status' => true,
                    'message' => 'slug not found'
                ],
                'data' => []
            ], 404);

        }

        return Response::json([
            'error' => [
                'status' => false,
                'message' => 'success'
            ],
            'data' => [
                'location_types' => $location_type
            ]
        ]);
        
    }

    public function getLocation($location_type_slug, $location_id){

        $location_type = LocationType::select('id')
            ->where('slug', $location_type_slug)
            ->firstOrFail();
        
        if(!$location_type){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'slug not found',
                status_code: 404
            );

        }


        $location = Location::select('location_type_id','name','id','fips','geopolitical_id')
            ->where([['id', $location_id], ['location_type_id', $location_type->id]])
            ->firstOrFail();
        
        if(!$location){

            return StandardizeResponse::APIResponse(
                error_status: true, 
                error_message: 'location id not found',
                status_code: 404
            );

        }
        
        return Response::json([
            'error' => [
                'status' => false,
                'message' => 'success'
            ],
            'data' => [
                'location' => $location
            ]
        ]);
    }

    public function getLocationIndicators($location_type_slug, $location_id){
        

        $location_type = LocationType::where('slug', $location_type_slug)
            ->select('id')
            ->firstOrFail();

        if(!$location_type){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'slug not found',
                status_code: 404
            );
        }

        $location_indicators = Location::where('id', $location_id)
            ->select('id','name', 'fips', 'geopolitical_id')
            ->withIndicators()
            ->get();

        if($location_indicators->isEmpty()){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'location id not found',
                status_code: 404
            );
        }

        return StandardizeResponse::APIResponse(
            data: $location_indicators
        );
        
    }

    public function getLocationIndicator($location_type_slug, $location_id, $indicator_slug ){

        $location_type = LocationType::where('slug', $location_type_slug)
            ->select('id')
            ->firstOrFail();

        if(!$location_type){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'slug not found',
                status_code: 404
            );
        }


        $location_indicator = Location::where('id', $location_id)
            ->select('id','name', 'fips', 'geopolitical_id')
            ->withIndicator($indicator_slug)
            ->get();

        if($location_indicator->isEmpty()){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'location id not found',
                status_code: 404
            );
        }


        return $location_indicator;
        
    }

    public function getLocationIndicatorData(Request $request, $location_type_slug, $location_id, $indicator_slug){

        $timeframe = $request->has('timeframe') ? $request->timeframe : null;

        $breakdown = $request->has('breakdown') ? $request->breakdown: null;

        $data_format = $request->has('data_format') ? $request->data_format : null;

        $offset = $request->has('offset') ? $request->offset : 0;

        $limit = $request->has('limit') ? $request->limit : 3000;

        $as = $request->has('as') ? $request->as : 'json';

        $wants_geojson = false;

        $accepts_geojson = str_contains($request->header('Accept'), 'application/geo+json');
        
        if($as === 'geojson' || $accepts_geojson) {
            $wants_geojson = true;
        }

        $location = Location::select('id')
            ->where('id', $location_id)
            ->firstOrFail();

        $indicator_data = Indicator::select('id', 'slug', 'name')
            ->where('slug', $indicator_slug)
            ->withDataDetails(
                breakdown: $breakdown,
                timeframe: $timeframe,
                data_format: $data_format,
                offset: $offset,
                limit: $limit,
                wants_geojson: $wants_geojson,
                location: $location->id
            )->get();

        if($wants_geojson){

            return Indicator::getDataAsGeoJSON($indicator_data);
        }

        return $indicator_data;
    
    }

}
