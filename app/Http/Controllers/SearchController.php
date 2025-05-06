<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Illuminate\Http\Request;
use App\Models\IndicatorEmbedding;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function getAISearchResults(Request $request){

        $search = $request->search;

        $search_cleaned = Str::of($search)
            ->lower()                        
            ->replaceMatches('/[^\w\s]/', '')
            ->squish()                        
            ->__toString(); 
    
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

        return $indicatorEmbedding->getSimilarIndicators($search_embedding_string, 0.9);

    }
}
