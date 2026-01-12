<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Models\LocationType;
use App\Http\Resources\LocationTypeResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LocationService;
use App\Services\IndicatorService;
use App\Http\Resources\IndicatorResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class LocationTypesController extends Controller
{

    use HandlesAPIRequestOptions;


    public function index(){

        $location_types = LocationService::queryAllLocationTypes();

        return response()->json([
            'data' => LocationTypeResource::collection($location_types)
        ]);
    }

    public function show(LocationType $location_type){

        return response()->json([
            'data' => new LocationTypeResource($location_type)
        ]);
    }

    public function indicatorIndex(LocationType $location_type){
        
        return response()->json([
            'data' => new LocationTypeResource(LocationService::queryLocationTypeIndicators($location_type))
        ]);
        
    }

    public function indicatorSearch(Request $request, LocationType $location_type){

        try {
            
            $search = $this->q($request);


        } catch(ValidationException $exception){
            
            return response()->json([

                'message' => $exception->getMessage()

            ], 400);

        }

        $location = LocationService::queryLocationTypeIndicators($location_type);

        $indicator_ids = $location->indicators->pluck('id')->toArray();

        $indicator_keyword_search = IndicatorService::querySearch($search, $indicator_ids);

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

        $indicator_semantic_search = IndicatorService::queryEmbeddings($search_embedding, 0.9, 20, $indicator_ids);

        $indicator_semantic_search_scored = IndicatorService::scoreSemanticSearchResults($indicator_semantic_search);

        $results = IndicatorService::rankSearchResults($indicator_keyword_search_scored, $indicator_semantic_search_scored);

        return response()->json([
            'data' => IndicatorResource::collection($results)
        ]);

    }
}
