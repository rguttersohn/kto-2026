<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use Illuminate\Support\Facades\Response;
use App\Models\Location;
use App\Support\StandardizeResponse;
use Illuminate\Http\Request;
use App\Models\Indicator;
use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationIndicatorResource;
use App\Http\Resources\LocationIndicatorsResource;
use App\Http\Resources\LocationIndicatorDataResource;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\LocationIndicatorFiltersResource;

class LocationsController extends Controller
{
    use HandlesAPIRequestOptions;

    public function getLocations(){

        $locations = Location::select('location_type_id','name','id','fips','geopolitical_id')->get();

       return StandardizeResponse::internalAPIResponse(
            data: LocationResource::collection($locations)
        );
    }

    public function getLocation($location_id){

    
        $location = Location::select('location_type_id','name','id','fips','geopolitical_id')
            ->where('id', $location_id)
            ->first();
        
        if(!$location){

            return StandardizeResponse::internalAPIResponse(
                error_status: true, 
                error_message: 'id not found',
                status_code: 404
            );

        }
        
        return StandardizeResponse::internalAPIResponse(
            data: new LocationResource($location)
        );
    }

    public function getLocationIndicators($location_id){
        
        $location_indicators = Location::where('id', $location_id)
            ->select('id','name', 'fips', 'geopolitical_id')
            ->withIndicators()
            ->first();
        
        if(!$location_indicators){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 404
            );
        }

        return StandardizeResponse::internalAPIResponse(
            data: new LocationIndicatorsResource($location_indicators)
        );
        
    }

    public function getLocationIndicator($location_id, $indicator_id ){


        $location_indicator = Location::where('id', $location_id)
            ->select('id','name', 'fips', 'geopolitical_id')
            ->withIndicator($indicator_id)
            ->first();
        

        if(!$location_indicator){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'location id not found',
                status_code: 404
            );
        }
        
        return StandardizeResponse::internalAPIResponse(
            data: new LocationIndicatorResource($location_indicator)
        );
        
    }

    public function getLocationIndicatorData(Request $request, $location_id, $indicator_id){
        
        $offset = $request->has('offset') ? $request->offset : 0;

        $limit = $request->has('limit') ? $request->limit : 3000;

        $wants_geojson = $this->wantsGeoJSON($request);
    
        $location = Location::select('id', 'name', 'fips', 'geopolitical_id')
            ->where('id', $location_id)
            ->first();
        
        if(!$location){

            return StandardizeResponse::internalAPIResponse(
                error_status: true, 
                error_message: 'location id not found',
                status_code: 404
            );
        }

        $indicator_data = Indicator::select('id', 'slug', 'name')
            ->where('id', $indicator_id)
            ->withDataDetails(
                limit: $limit,
                offset: $offset,
                wants_geojson: $wants_geojson,
                filters: $request->input('filter', []),
                sorts: $request->input('sort', [])
            )->first();
    

        if($wants_geojson){

            return Indicator::getDataAsGeoJSON($indicator_data);
        }

        $data = [
                'location' => $location,
                'indicator' => $indicator_data
        ];

        return StandardizeResponse::internalAPIResponse(
            data: new LocationIndicatorDataResource($data)
        );
    
    }


    public function getLocationIndicatorFilters($location_id, $indicator_id){

        $location = Location::select('id','name', 'fips', 'geopolitical_id')
            ->where('id', $location_id)
            ->first();

        if(!$location){

            return StandardizeResponse::internalAPIResponse(
                error_status: true, 
                error_message: 'id not found',
                status_code: 400
            );
        }

        $filters = Indicator::select('id', 'slug', 'name')
            ->where('id', $indicator_id)
            ->withAvailableFilters()
            ->first();

        $formatted_filters = IndicatorFiltersFormatter::formatFilters($filters);

        $data = [
            'location' => $location,
            'indicator' => $formatted_filters
        ];

        return StandardizeResponse::internalAPIResponse(
            data: new LocationIndicatorFiltersResource($data)
        );

    }

}
