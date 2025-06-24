<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indicator;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Support\StandardizeResponse;
use App\Http\Resources\IndicatorResource;
use App\Http\Resources\IndicatorsResource;
use App\Http\Resources\IndicatorFiltersResource;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\IndicatorGeoJSONDataResource;
use Illuminate\Validation\ValidationException;
use App\Models\DataIndicator;
use App\Support\GeoJSON;

class IndicatorsController extends Controller
{
    use HandlesAPIRequestOptions;
    
    public function getIndicators(){

        $indicators = Indicator::select('id', 'name', 'slug')->get();

        return StandardizeResponse::internalAPIResponse(
            data: IndicatorsResource::collection($indicators)
        );

    }

    public function getIndicator(Request $request, $indicator_id){

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
        

        $indicator = Indicator::select('id', 'name', 'slug', 'definition','note', 'source')
            ->where('id', $indicator_id)
            ->with(['data' => fn($query)=>$query->withDetails(
                    limit: $limit,
                    offset: $offset,
                    wants_geojson: $wants_geojson,
                    filters: $filters,
                    sorts: $sorts
                )
            ])
            ->first();


        if(!$indicator){
        
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 400
            );
        }


        return StandardizeResponse::internalAPIResponse(
            data: new IndicatorResource($indicator)
        );
    
    }

    public function getIndicatorData(Request $request, $indicator_id){

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
        
       
        $indicator_data = DataIndicator::withDetails(
                    limit: $limit,
                    offset: $offset,
                    wants_geojson: $wants_geojson,
                    filters: $filters,
                    sorts: $sorts
                    )
            ->where('indicator_id', $indicator_id)
            ->get();
        
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

    public function getIndicatorFilters($indicator_id){
        
        $indicator_filters = Indicator::select('id', 'name', 'slug')
            ->withAvailableFilters()
            ->where('id', $indicator_id)
            ->first();
            
        
        if(!$indicator_filters){
            
            return StandardizeResponse::internalAPIResponse(
                error_status: false,
                error_message: 'id not found',
                status_code: 400
            );

        }

        $filters_formatted = IndicatorFiltersFormatter::formatFilters($indicator_filters);

        return StandardizeResponse::internalAPIResponse(
            data: new IndicatorFiltersResource($filters_formatted['data'])
        );
    }
    
}


