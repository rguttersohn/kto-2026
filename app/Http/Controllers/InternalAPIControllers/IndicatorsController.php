<?php

namespace App\Http\Controllers\InternalAPIControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Http\Resources\IndicatorGeoJSONDataResource;
use Illuminate\Validation\ValidationException;
use App\Services\IndicatorService;
use App\Support\GeoJSON;
use App\Http\Controllers\Controller;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\IndicatorDataCountResource;
use App\Http\Resources\IndicatorResource;
use App\Models\Indicator;
use Illuminate\Support\Facades\Log;

class IndicatorsController extends Controller
{
    use HandlesAPIRequestOptions;

    public function index(){
        
        $indicators = IndicatorService::queryAllIndicators();

        return response()->json([
            'data' => IndicatorResource::collection($indicators)
        ]);

    }

    public function show(Indicator $indicator){

        return response()->json([
            'data' => new IndicatorResource($indicator)
        ]);

    }
    
    public function data(Request $request, Indicator $indicator){

        try {

            $offset = $this->offset($request);

            $limit = $this->limit($request);

            $wants_geojson = $this->wantsGeoJSON($request);

            $request_filters = $this->filters($request);

            $sorts = $this->sorts($request);

            $merge_defaults = $this->wantsMergeDefaultFilters($request);

            $excluded_default_filters = $this->excludedDefaultFilters($request);

        } catch (ValidationException $exception){

            return response()->json([

                'message' => $exception->getMessage()

            ], 400);
        }

        if($merge_defaults){

            IndicatorService::queryIndicatorFilters($indicator);

            $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator->filters, $request_filters, $excluded_default_filters);

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
        
        try {

            $request_filters = $this->filters($request);

            $merge_defaults = $this->wantsMergeDefaultFilters($request);

            $excluded_default_filters = $this->excludedDefaultFilters($request);

        } catch(ValidationException $exception) {

            return response()->json([
                
                'message' => $exception->getMessage()

            ], 400);

        }
        
        if($merge_defaults){

            IndicatorService::queryIndicatorFilters($indicator);

            $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator->filters, $request_filters, $excluded_default_filters);

        } else {

            $filters = $request_filters;
        }

        $count = IndicatorService::queryDataCount($indicator->id, $filters);

        return response()->json([
            'data' => new IndicatorDataCountResource($count)
        ]);
        
    }

    public function export(Request $request, Indicator $indicator){

        try {

            $request_filters = $this->filters($request);

            $wants_geojson = $this->wantsGeoJSON($request);

            $wants_csv = $this->wantsCSV($request);

            $sorts = $this->sorts($request);

        } catch (ValidationException $exception){

            return response()->json([

                'message' => $exception->getMessage()

            ] ,400);
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


    /**
     * 
     * Handles returning available filters for an indicator
     * 
     */

    public function availableFilters(Indicator $indicator){
        
        IndicatorService::queryIndicatorFilters($indicator);

        return response()->json([
            'data' => new IndicatorResource($indicator)
        ]);
    }

    /**
     * 
     * Handles hybrid searching for indicators
     * 
     */

    public function search(Request $request){

        try {

            $search = $this->q($request);

        } catch(ValidationException $exception){

            return response()->json([

                'message' => $exception->getMessage()

            ],400);

        }
        

        $indicators_keyword = IndicatorService::querySearch($search);
        
        $indicators_keyword_scored = IndicatorService::scoreKeywordSearchResults($indicators_keyword);
    
        $embed_response = IndicatorService::fetchSearchAsVector($search);

        if(!$embed_response->successful()){

            Log::debug("Creating text embedding for search '$search' failed");

            return response()->json([

                'message'=> "Failed to create embedding for '$search'"
            
            ], 500);
            
        }

        $body = json_decode($embed_response->body());

        $search_embedding = $body->embedding;

        $indicators_semantic = IndicatorService::queryEmbeddings($search_embedding, 0.9, 20);

        $indicators_semantic_scored = IndicatorService::scoreSemanticSearchResults($indicators_semantic);
                
        $results = IndicatorService::rankSearchResults($indicators_keyword_scored, $indicators_semantic_scored);

        return response()->json([
            'data' => IndicatorResource::collection($results)
        ]);

    }
   
}


