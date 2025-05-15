<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indicator;
use Illuminate\Support\Facades\Response;

class IndicatorsController extends Controller
{
    
    public function getIndicators(){

        $indicators = Indicator::select('id', 'name', 'slug')->get();

        return Response::json([
            'error' => [
                'status' => false, 
                'message' => 'success'
            ],
            'data' => [
                'indicators' => $indicators
            ]
        ]);

    }

    public function getIndicator($indicator_slug){

        $indicator = Indicator::select('id', 'name', 'slug', 'definition','note', 'source')
            ->where('slug', $indicator_slug)
            ->get();


        if($indicator->isEmpty()){
        
            return Response::json(
                [
                'error' => [
                'status' => true, 
                'message' => 'slug not found'
                ],
                'data' => []
            ], 404);
        }


        return Response::json([
            'error' => [
                'status' => false, 
                'message' => 'success'
            ],
            'data' => [
                'indicator' => $indicator
            ]
        ]);
        
        
    }

    public function getIndicatorData(Request $request, $indicator_slug){

        $timeframe = $request->has('timeframe') ? $request->timeframe : null;

        $breakdown = $request->has('breakdown') ? $request->breakdown: null;

        $data_format = $request->has('data_format') ? $request->data_format : null;

        $location = $request->has('location') ? $request->location: null;

        $location_type = $request->has('location_type') ? $request->location_type: null;

        $offset = $request->has('offset') ? $request->offset : 0;

        $limit = $request->has('limit') ? $request->limit : 3000;

        $as = $request->has('as') ? $request->as : 'json';

        $wants_geojson = false;

        $accepts_geojson = str_contains($request->header('Accept'), 'application/geo+json');
        
        if($as === 'geojson' || $accepts_geojson) {
            $wants_geojson = true;
        }
        
        $indicator = Indicator::select('id', 'name', 'slug')
            ->withDataDetails(
                    breakdown: $breakdown, 
                    timeframe: $timeframe,
                    location: $location,
                    location_type: $location_type,
                    data_format: $data_format,
                    limit: $limit,
                    offset: $offset,
                    wants_geojson: $wants_geojson
                    )
            ->where('slug', $indicator_slug)
            ->get();
        
        if($indicator->isEmpty()){
            
            return Response::json(
                [
                'error' => [
                'status' => true, 
                'message' => 'slug not found'
                ],
                'data' => []
            ], 404);
        }
        
        if($wants_geojson){
          
            $indicator_geojson = Indicator::getDataAsGeoJSON($indicator);

            return Response::json([
                'error' => [
                    'status' => false, 
                    'message' => 'success'
                ],
                'data' => [
                    'indicator' => $indicator_geojson
                ]
            ]);

        }
        

        return Response::json([
            'error' => [
                'status' => false, 
                'message' => 'success'
            ],
            'data' => [
                'indicator' => $indicator
            ]
            ]);
    }

    public function getIndicatorFilters($indicator_slug){
        
        
        $indicator_filters = Indicator::select('id', 'name', 'slug')
            ->withAvailableFilters()
            ->where('slug', $indicator_slug)
            ->get();
            
        
        if($indicator_filters->isEmpty()){
            
            return Response::json(
                [
                'error' => [
                'status' => true, 
                'message' => 'slug not found'
                ],
                'data' => []
            ], 404);
        }

        $formatted_filters = Indicator::formatFilters($indicator_filters);

        return Response::json([
            'error' => [
                'status' => false, 
                'message' => 'success'
            ],
            'data' => [
                'filters' => $formatted_filters
            ]
        ]);

    
    }
    
}


