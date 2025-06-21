<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indicator;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Support\StandardizeResponse;
use App\Http\Resources\IndicatorResource;
use App\Http\Resources\IndicatorsResource;
use App\Http\Resources\IndicatorFiltersResource;

class IndicatorsController extends Controller
{
    use HandlesAPIRequestOptions;
    
    public function getIndicators(){

        $indicators = Indicator::select('id', 'name', 'slug')->get();

        return StandardizeResponse::internalAPIResponse(
            data: IndicatorsResource::collection($indicators)
        );

    }

    public function getIndicator($indicator_id){

        $indicator = Indicator::select('id', 'name', 'slug', 'definition','note', 'source')
            ->where('id', $indicator_id)
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

        $offset = $request->has('offset') ? $request->offset : 0;

        $limit = $request->has('limit') ? $request->limit : 3000;

        $wants_geojson = $this->wantsGeoJSON($request);
        
        $indicator = Indicator::select('id', 'name', 'slug')
            ->withDataDetails(
                    limit: $limit,
                    offset: $offset,
                    wants_geojson: $wants_geojson,
                    filters: $request->input('filter', []),
                    sorts: $request->input('sort', [])
                    )
            ->where('id', $indicator_id)
            ->first();
        
        if(!$indicator){
            
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 400
            );
        }
        
        if($wants_geojson){
          
            $indicator_geojson = IndicatorDataResource::getDataAsGeoJSON($indicator);

            return StandardizeResponse::internalAPIResponse(
                data: new IndicatorDataResource($indicator_geojson)
            );

        }
        
        return StandardizeResponse::internalAPIResponse(
            data: new IndicatorDataResource($indicator)
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

        $filters_formatted = IndicatorFiltersResource::formatFilters($indicator_filters);

        return StandardizeResponse::internalAPIResponse(
            data: new IndicatorFiltersResource($filters_formatted)
        );
    }
    
}


