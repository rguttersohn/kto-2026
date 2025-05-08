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

    public function getIndicator(Request $request, $indicator_slug){

        $timeframe = $request->has('timeframe') ? $request->timeframe : null;

        $breakdown = $request->has('breakdown') ? $request->breakdown: null;

        $data_format = $request->has('data_format') ? $request->data_format : null;

        $location = $request->has('location') ? $request->location: null;

        $location_type = $request->has('location_type') ? $request->location_type: null;

        $indicator = Indicator::select('id', 'name', 'slug')
            ->withDataDetails(
                    breakdown: $breakdown, 
                    timeframe: $timeframe,
                    location: $location,
                    location_type: $location_type,
                    data_format: $data_format
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
    
}
