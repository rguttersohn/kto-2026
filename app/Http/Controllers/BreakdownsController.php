<?php

namespace App\Http\Controllers;

use App\Models\Breakdown;
use Illuminate\Support\Facades\Response;

class BreakdownsController extends Controller
{

    public function getBreakdowns(){
        
        $breakdowns = Breakdown::with(['subBreakdowns' => function($query){
            return $query->select('id','parent_id', 'name', 'slug');
        }])
            ->whereNull('parent_id')
            ->select('id', 'parent_id', 'name', 'slug')
            ->get();

        return Response::json([
            'error' => [
                'status' => false,
                'message' => 'Success'
            ],
            'data' => [
                'breakdowns' => $breakdowns
            ]
        ]);

    }


    public function getBreakdown($breakdown_slug){
        
        $breakdown = Breakdown::with(['subBreakdowns' => function($query){
            return $query->select('id','parent_id', 'name', 'slug');
        }])
            ->whereNull('parent_id')
            ->where('slug', $breakdown_slug)
            ->select('id', 'parent_id', 'name', 'slug')
            ->get();

        if($breakdown->isEmpty()){

            return Response::json([
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
                'breakdown' => $breakdown
            ]
        ]);

    }
}
