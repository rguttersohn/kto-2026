<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Models\Location;
use App\Support\StandardizeResponse;
use Illuminate\Http\Request;
use App\Models\Indicator;
use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationIndicatorResource;
use App\Http\Resources\LocationIndicatorsResource;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\LocationIndicatorFiltersResource;
use App\Models\DataIndicator;
use Illuminate\Validation\ValidationException;

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
            ->with(['indicators' => fn($query)=>$query->select('indicators.id', 'name')])
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

    public function getLocationIndicator(Request $request, $location_id, $indicator_id ){

        $offset = $this->offset($request);

        $limit = $this->limit($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $filters = $this->filters($request);

        $sorts = $this->sorts($request);

        if($offset instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $offset->getMessage(),
                status_code: 400
            );
        }

        if($limit instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $limit->getMessage(),
                status_code: 400
            );
        }

        if($filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $filters->getMessage(),
                status_code: 400
            );
        }

        if($sorts instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $sorts->getMessage(),
                status_code: 400
            );

        }

        $location_indicator = Location::where('id', $location_id)
            ->select('id','name', 'fips', 'geopolitical_id')
            ->with(['indicators' => function($query)use($indicator_id, $limit, $offset, $wants_geojson, $filters, $sorts){
                    $query->where('indicators.id', $indicator_id)
                        ->with(['data' => function($query)use($limit, $offset, $wants_geojson, $filters, $sorts){
                            $query->withDetails(
                                limit: $limit,
                                offset: $offset,
                                wants_geojson: $wants_geojson,
                                filters: $filters,
                                sorts: $sorts
                            );
                        }])
                ;
            }])
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
        
        $offset = $this->offset($request);

        $limit = $this->limit($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $filters = $this->filters($request);

        $sorts = $this->sorts($request);

        if($offset instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $offset->getMessage(),
                status_code: 400
            );
        }

        if($limit instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $limit->getMessage(),
                status_code: 400
            );
        }

        if($filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $filters->getMessage(),
                status_code: 400
            );
        }

        if($sorts instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $sorts->getMessage(),
                status_code: 400
            );

        }
    
        $data = DataIndicator::withDetails(
            limit: $limit,
            offset: $offset,
            wants_geojson: $wants_geojson,
            filters: $filters,
            sorts: $sorts
            )
            ->where('indicator_id', $indicator_id)
            ->get();

        if(!$data){

            return StandardizeResponse::internalAPIResponse(
                error_status: true, 
                error_message: 'location id not found',
                status_code: 400
            );
        }


        return StandardizeResponse::internalAPIResponse(
            data: IndicatorDataResource::collection($data)
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
