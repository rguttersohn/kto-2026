<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Illuminate\Http\Request;
use App\Models\IndicatorEmbedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use App\Support\EmbeddingTextSanitizer;

class SearchController extends Controller
{
    public function getAISearchResults(Request $request){

        $search = $request->search;

        $search_cleaned = EmbeddingTextSanitizer::sanitize($search);
    
        $embed_response = Http::withHeaders([
            'Authorization' => "Bearer " . env('SUPABASE_EMBED_AUTH'),
            'Content-Type' => 'application/json',
        ])->post(env('SUPABASE_EMBED_ENDPOINT'),[
            'name' => 'Functions',
            'input' => "Find information about: " . $search_cleaned
        ]);

        if(!$embed_response->successful()){

            return Response::json([
                'error' => [
                    'status' => true,
                    'message' => 'Failed to create embedding.'
                ],
                'data' => []
            ], 500);
            
        }

        $indicatorEmbedding = new IndicatorEmbedding();

        $body = json_decode($embed_response->body());

        $search_embedding = $body->embedding;

        $search_embedding_string = '[' . implode(',', $search_embedding) . ']';

        $indicators = $indicatorEmbedding->getSimilarIndicators($search_embedding_string, 0.9, 10);

        return Response::json([
            'error' =>[
                'status' => false,
                'message' => 'Success'
            ],
            'data' => [
                'indicators' => $indicators
            ] 
        ]);

    }

    public function getKeywordSearchResults(Request $request){

        if(!$request->has('search')){

            return Response::json([
                'error' => [
                    'status' => true,
                    'message' => 'Missing search parameter'
                ],
                'data' => null
            ], 400);

        }
        
        $query = $request->search;

        $indicators = Indicator::search($query)->take(10)->get();

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
}
