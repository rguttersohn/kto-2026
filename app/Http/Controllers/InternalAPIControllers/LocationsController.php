<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Models\Location;
use App\Support\StandardizeResponse;
use Illuminate\Http\Request;
use App\Models\Indicator;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\LocationIndicatorFiltersResource;
use Illuminate\Validation\ValidationException;
use App\Services\IndicatorService;
use App\Http\Controllers\Controller;
use App\Http\Resources\IndicatorFiltersResource;

class LocationsController extends Controller
{
    use HandlesAPIRequestOptions;

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

        $location = Location::find($location_id);

        if(!$location){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'location id not found',
                status_code: 400
            );

        }

        $indicator = Indicator::find($indicator_id);

        if(!$indicator){
           
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'indicator id not found',
                status_code: 400
            );

        }
    
        $data = IndicatorService::queryData(
            $indicator_id,
            $limit,
            $offset,
            $wants_geojson,
            $filters,
            $sorts,
            $location_id
        );

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

        $filters = Indicator::select('id', 'name')
            ->where('id', $indicator_id)
            ->withAvailableFilters()
            ->first();

        $formatted_filters = IndicatorFiltersFormatter::formatFilters($filters);

        return StandardizeResponse::internalAPIResponse(
            data: new IndicatorFiltersResource($formatted_filters['data'])
        );

    }

}
