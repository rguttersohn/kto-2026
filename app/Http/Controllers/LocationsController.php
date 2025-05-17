<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use App\Models\Location;
use App\Support\StandardizeResponse;
use Illuminate\Http\Request;
use App\Models\Indicator;

class LocationsController extends Controller
{
    
    public function getLocations(){

        $locations = Location::select('location_type_id','name','id','fips','geopolitical_id')->get();

       return StandardizeResponse::APIResponse(
            data: [
                'locations' => $locations
            ]
        );
    }

    public function getLocation($location_id){

    
        $location = Location::select('location_type_id','name','id','fips','geopolitical_id')
            ->where('id', $location_id)
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

    public function getLocationIndicators($location_id){
        
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

    public function getLocationIndicator($location_id, $indicator_slug ){


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

    public function getLocationIndicatorData(Request $request, $location_id, $indicator_slug){
        
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

        if(!$location){

            return StandardizeResponse::APIResponse(
                error_status: true, 
                error_message: 'location id not found',
                status_code: 404
            );
        }

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


    public function getLocationIndicatorFilters($location_id, $indicator_slug){


        $location = Location::select('id')
            ->where('id', $location_id)
            ->firstOrFail();

        if(!$location){

            return StandardizeResponse::APIResponse(
                error_status: true, 
                error_message: 'location id not found',
                status_code: 404
            );
        }

        $filters = Indicator::select('id', 'slug', 'name')
            ->where('slug', $indicator_slug)
            ->withAvailableFilters()
            ->get();

        $formatted_filters = Indicator::formatFilters($filters);

        return StandardizeResponse::APIResponse(
            data: $formatted_filters
        );

    }

}
