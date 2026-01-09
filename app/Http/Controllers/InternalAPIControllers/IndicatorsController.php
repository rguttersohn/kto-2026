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
use App\Http\Resources\IndicatorResource;

class IndicatorsController extends Controller
{
    use HandlesAPIRequestOptions;

    public function index(){
        
        $indicators = IndicatorService::queryAllIndicators();

        return response()->json([
            'indicators' => IndicatorResource::collection($indicators)
        ]);

    }

    public function show(Indicator $indicator){

        return response()->json([
            'indicator' => new IndicatorResource($indicator)
        ]);

    }
    
    public function data(Request $request, Indicator $indicator){

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

            $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator->id);

            $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

            $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator_filters, $request_filters);

        } else {

            $filters = $request_filters;
        }

        $indicator_data = IndicatorService::queryData(
            indicator_id: $indicator->id, 
            limit: $limit, 
            offset: $offset,
            wants_geojson: $wants_geojson,
            filters: $filters,
            sorts: $sorts
        );
        
        if($wants_geojson){
          
            return response()->json([

                'data' => GeoJSON::wrapGeoJSONResource(IndicatorGeoJSONDataResource::collection($indicator_data))

            ]);

        }
        
        return response()->json([
            'data' => IndicatorDataResource::collection($indicator_data)
        ]);
    }

    public function count(Request $request, Indicator $indicator){

        $request_filters = $this->filters($request);

        if($request_filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $request_filters->getMessage(),
                status_code: 400
            );
        }

        $merge_defaults = $this->wantsMergeDefaults($request);

        if($merge_defaults){

            $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator->id);

            $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

            $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator_filters, $request_filters);

        } else {

            $filters = $request_filters;
        }

        $count = IndicatorService::queryDataCount($indicator->id, $filters);

        return response()->json([
            'data' => new IndicatorDataCountResource($count)
        ]);
        
    }

    public function export(Request $request, Indicator $indicator){

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

        $indicator = Indicator::find($indicator->id);

        if(!$indicator){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'indicator id not found',
                status_code: 404
            );

        }

        $indicator_data = IndicatorService::queryDataWithoutLimit($indicator->id, $wants_geojson, $request_filters, $sorts );

        if($wants_geojson){
            
            $filename = 'indicator_' . $indicator->id . '_data.json';

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
           
            $filename = 'indicator_' . $indicator->id . '_data.csv';
        
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
        
        $filename = 'indicator_' . $indicator->id . '_data.json';


        return response()->streamDownload(function() use($indicator_data){

            $output = fopen('php://output', 'w');

            fwrite($output, json_encode($indicator_data));

            fclose($output);

        },$filename,[
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

    
    }

    public function getFilters(Indicator $indicator){

        $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator->id);

        $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted);

        return response()->json([
            'data' => $indicator_filters['data']
        ]);
    }

    
}


