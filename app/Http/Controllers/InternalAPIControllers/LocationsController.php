<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationGeoJSONResource;
use App\Http\Resources\LocationResource;
use App\Services\LocationService;
use App\Models\LocationType;
use App\Http\Resources\LocationTypeResource;
use App\Support\GeoJSON;
use Illuminate\Validation\ValidationException;
use App\Services\IndicatorService;
use Illuminate\Support\Facades\Log;


class LocationsController extends Controller
{
    use HandlesAPIRequestOptions;
    
    public function index(LocationType $location_type){

        $wants_geojson = $this->wantsGeoJSON(request());

        $locations = LocationService::queryLocationTypeWithLocation($location_type->id, $wants_geojson);

        return response()->json([
            'data' => new LocationTypeResource($locations)
        ]);

    }

    public function show(Request $request, int $location_id){

        $wants_geojson = $this->wantsGeoJSON($request);

        $location = LocationService::queryLocation($location_id, $wants_geojson);

        if($wants_geojson){
 
            return response()->json([
                'data' => Geojson::wrapGeoJSONResource(new LocationGeoJSONResource($location))
            ]);
        }

        return response()->json([
            'data' => new LocationResource($location)
        ]);

    }

      /**
     * 
     * handles indicator index for all indicators available to the location type. 
     * 
     * Also if any filter or search params are present it handles filtering by search and filters
     * 
     */

    public function indicatorIndex(Request $request, Location $location){

        if($request->query->count() === 0){

            $location_w_indicators = LocationService::queryLocationIndicators($location);

            return response()->json([

                'data' => new LocationResource($location_w_indicators)

            ]);
            
        }

        try {
            
            $search = $this->q($request);

            $filters = $this->filters($request);

        } catch(ValidationException $exception){
            
            return response()->json([

                'message' => $exception->getMessage()

            ], 422);

        }

        if(!$search){

            $location_w_indicators = LocationService::queryLocationIndicators($location, $filters);

            return response()->json([
                
                'data' => new LocationResource($location_w_indicators)

            ]);
        }

        $indicator_ids = $location->indicators->pluck('id')->toArray();

        $indicator_keyword_search = IndicatorService::querySearch($search, $filters, $indicator_ids);

        $indicator_keyword_search_scored = IndicatorService::scoreKeywordSearchResults($indicator_keyword_search);
        
        $embed_response = IndicatorService::fetchSearchAsVector($search);

        if(!$embed_response->successful()){

            Log::debug("Creating text embedding for search '$search' failed");

            return response()->json([

                'message'=> "Failed to create embedding for '$search'"
            
            ], 500);
            
        }

        $body = json_decode($embed_response->body());

        $search_embedding = $body->embedding;

        $indicator_semantic_search = IndicatorService::queryEmbeddings($search_embedding, 0.9, $filters, 20, $indicator_ids);

        $indicator_semantic_search_scored = IndicatorService::scoreSemanticSearchResults($indicator_semantic_search);

        $results = IndicatorService::rankSearchResults($indicator_keyword_search_scored, $indicator_semantic_search_scored);

        $location->indicators = $results;
        
        return response()->json([

            'data' => new LocationResource($location)

        ]);

    }

}
