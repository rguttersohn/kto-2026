<?php

namespace App\Http\Controllers\InternalAPIControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Support\StandardizeResponse;
use App\Http\Resources\IndicatorGeoJSONDataResource;
use Illuminate\Validation\ValidationException;
use App\Services\IndicatorService;
use App\Support\GeoJSON;
use App\Http\Controllers\Controller;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\IndicatorDataCountResource;

class IndicatorsController extends Controller
{
    use HandlesAPIRequestOptions;
    

    public function getIndicatorData(Request $request, $indicator_id){

        $offset = $this->offset($request);

        $limit = $this->limit($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $request_filters = $this->filters($request);

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

        if($request_filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $request_filters->getMessage(),
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

    
        $merge_defaults = $this->mergeDefaults($request);

        if($merge_defaults){

            $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

            $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

            $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator_filters, $request_filters);

        } else {

            $filters = $request_filters;
        }

        $indicator_data = IndicatorService::queryData(
            indicator_id: $indicator_id, 
            limit: $limit, 
            offset: $offset,
            wants_geojson: $wants_geojson,
            filters: $filters,
            sorts: $sorts
        );
        
        if($indicator_data->isEmpty()){
            
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 400
            );
        }
        
        if($wants_geojson){
          
            return StandardizeResponse::internalAPIResponse(

                data: GeoJSON::wrapGeoJSONResource(IndicatorGeoJSONDataResource::collection($indicator_data))
            );

        }
        
        return StandardizeResponse::internalAPIResponse(
            data: IndicatorDataResource::collection($indicator_data)
        );
    }



    public function getIndicatorDataCount(Request $request, $indicator_id){

        $request_filters = $this->filters($request);

        if($request_filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $request_filters->getMessage(),
                status_code: 400
            );
        }

        $merge_defaults = $this->mergeDefaults($request);

        if($merge_defaults){

            $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

            $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

            $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator_filters, $request_filters);

        } else {

            $filters = $request_filters;
        }


        $count = IndicatorService::queryDataCount($indicator_id, $filters);

        return StandardizeResponse::internalAPIResponse(
            data: new IndicatorDataCountResource($count)
        );
    }

    
}


