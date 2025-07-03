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
use App\Models\Indicator;

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

    
        $merge_defaults = $this->wantsMergeDefaults($request);

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

        $indicator = Indicator::find($indicator_id);

        if(!$indicator){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'indicator id not found',
                status_code: 400
            );
        }

        $merge_defaults = $this->wantsMergeDefaults($request);

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

    public function getIndicatorExport(Request $request, $indicator_id){

        $request_filters = $this->filters($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $wants_csv = $this->wantsCSV($request);

        $sorts = $this->sorts($request);

        if($request_filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $request_filters->getMessage(),
                status_code: 400
            );
        }

        $indicator = Indicator::find($indicator_id);

        if(!$indicator){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'indicator id not found',
                status_code: 404
            );

        }

        $indicator_data = IndicatorService::queryDataWithoutLimit($indicator_id, $wants_geojson, $request_filters, $sorts );

        if($wants_geojson){
            
            $filename = 'indicator_' . $indicator_id . '_data.json';

            return response()->streamDownload(function () use ($indicator_data) {
                $output = fopen('php://output', 'w');
            
                $geojson = GeoJSON::wrapGeoJSONResource(
                    IndicatorGeoJSONDataResource::collection($indicator_data)
                );
            
                fwrite($output, json_encode($geojson));
            
                fclose($output);
            }, $filename, [ 
                'Content-Type' => 'application/geo+json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        if ($wants_csv) {
           
            $filename = 'indicator_' . $indicator_id . '_data.csv';
        
            return response()->streamDownload(function () use ($indicator_data) {
                
                $output = fopen('php://output', 'w');
        
                $first = $indicator_data->first();
                $headers = array_keys($first->toArray());
                $headers_filtered = array_filter($headers, function($header){
                    return !in_array($header, ['indicator_id', 'location_id', 'location_type_id']);
                });
        
                fputcsv($output, $headers_filtered);
        
                foreach ($indicator_data->toArray() as $row) {
                    fputcsv($output, [
                        $row['data'],
                        $row['location'],
                        $row['location_type'],
                        $row['timeframe'],
                        $row['breakdown_name'],
                        $row['format']
                    ]);
                }
        
                fclose($output);

            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }
        
        $filename = 'indicator_' . $indicator_id . '_data.json';


        return response()->streamDownload(function() use($indicator_data){

            $output = fopen('php://output', 'w');

            fwrite($output, json_encode($indicator_data));

            fclose($output);

        },$filename,[
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

    
    }

    
}


