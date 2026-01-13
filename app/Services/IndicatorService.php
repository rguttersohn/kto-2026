<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\Indicator;
use Illuminate\Database\Eloquent\Model;
use App\Models\IndicatorData;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\DB;

class IndicatorService {


    /**
     * 
     * Queries all indicators
     * 
     */
    public static function queryAllIndicators():Collection{

        return Indicator::all();
    }

    /**
     * 
     * Queries a single indicator
     * 
     */

    public static function queryIndicator($indicator_id):Model | null{

        return Indicator::select('id', 'name', 'definition','note', 'source')
            ->where('id', $indicator_id)
            ->first();
        
    }

    /**
     * 
     * 
     * 
     */

    public static function queryIndicatorWithData($indicator_id, $limit, $offset, $wants_geojson, $filters, $sorts):Model | null{
       
        return Indicator::select('id', 'name','definition','note', 'source')
            ->where('id', $indicator_id)
            ->with(['data' => fn($query)=>$query->withDetails(
                    limit: $limit,
                    offset: $offset,
                    wants_geojson: $wants_geojson,
                    filters: $filters,
                    sorts: $sorts
                )
            ])
            ->first();
    } 

    /**
     * 
     * Queries indicator data
     * 
     */

    public static function queryData(int $indicator_id, int $limit, int $offset, bool $wants_geojson, array $filters, array $sorts, ?int $location_id = null):Collection{
           return IndicatorData::withDetails(
                limit: $limit,
                offset: $offset,
                wants_geojson: $wants_geojson,
                filters: $filters,
                sorts: $sorts
                )
        ->where('indicator_id', $indicator_id)
        ->when($location_id, fn($query)=>$query->where('location_id', $location_id))
        ->get();
    }

    /**
     * 
     * Queries indicator data without enforcing limit. Useful for exporting as csv
     * 
     */

    public static function queryDataWithoutLimit(int $indicator_id, bool $wants_geojson, array $filters, array $sorts, ?int $location_id = null):Collection{
        return IndicatorData::withDetailsWithOutLimit(
             wants_geojson: $wants_geojson,
             filters: $filters,
             sorts: $sorts
             )
        ->where('indicator_id', $indicator_id)
        ->when($location_id, fn($query)=>$query->where('location_id', $location_id))
        ->get();
    }

    /**
     * 
     * Counts all indicator data after filters applied
     * 
     */
    public static function queryDataCount(int $indicator_id, array $filters, ?int $location_id = null):int{
        
        return IndicatorData::forCounting($filters)
            ->where('indicator_id', $indicator_id)
            ->when($location_id, fn($query)=>$query->where('location_id', $location_id))
            ->count();

    }


    public static function queryIndicatorFilters(int $indicator_id):Model | null{
        
        return Indicator::select('id', 'name')
            ->withAvailableFilters()
            ->where('id', $indicator_id)
            ->first();

    }

    public static function validateFilterNames(string $filter_name): string | bool {

        return match($filter_name){
                'breakdowns' => 'breakdowns',
                'timeframes' => 'timeframes',
                'locations' => 'locations',
                'imports' => 'imports',
                default => false
            };
    }

    public static function rememberFilter(int $indicator_id, string $filter_name, callable $callback):Collection {

        $validated_filter_name = static::validateFilterNames($filter_name);

        if(!$validated_filter_name){

            throw new Exception('Indicator filter name is not valid');
        }
        
        return Cache::tags(["indicator_$indicator_id","filters"])
            ->rememberForever("indicator_{$filter_name}_{$indicator_id}", $callback);

    }

    /**
     * 
     * Takes the search query from a user and turns it into an embedding by submitting to an LLM
     * 
     * @param search The search queyry. More useful if sanitized before passing an arg using the EmbeddingTextSanitizer class
     * 
     * @return Illuminate\Http\Client\Response Returns a response so the controller handle any errors communicating with the LLM

     */

    public static function fetchSearchAsVector(string $search):Response{

        return Http::withHeaders([
                'Authorization' => "Bearer " . config('services.supabase-embedding.auth'),
                'Content-Type' => 'application/json',
            ])->post(config('services.supabase-embedding.endpoint'),[
                'name' => 'Functions',
                'input' => "Find information about: " . $search
            ]);
    }


    public static function queryEmbeddings(array $search_embedding, float $threshold, int $limit=20, array | null $indicator_ids = null):Collection{

        $search_embedding_formatted = '[' . implode(',', $search_embedding) . ']';

        $where_clause = '';
        
        //init bindings
        $bindings = [$search_embedding_formatted, $search_embedding_formatted, $threshold];

        if($indicator_ids){

            //add the placeholder 
            $placeholders = implode(',', array_fill(0, count($indicator_ids), '?'));
            
            //add the where clause since indicator ids exist
            $where_clause = "AND indicator_id in ($placeholders)";
            
            // add indicator ids to the bindngs
            $bindings = array_merge($bindings, $indicator_ids);

        }
        
        //add in the limits to the end
        $bindings[] = $limit;

        $results = DB::connection('supabase')->select("
            SELECT i.*, e.embedding <=> ?::vector AS distance
            FROM indicators.indicator_embeddings e
            JOIN indicators.indicators i ON i.id = e.indicator_id
            WHERE e.embedding <=> ?::vector < ?
                $where_clause
            ORDER BY distance ASC
            LIMIT ?
        ", $bindings);

        //convert to collection of models before returning
        $collection = collect($results)->map(function($result) {
                
                $indicator = Indicator::make((array) $result);
                $indicator->exists = true; 
                $indicator->distance = $result->distance;
                return $indicator;

            });
        

        return $collection;
        
    }

    public static function querySearch(string $query, array | null $indicator_ids = null ):Collection{

        return Indicator::search($query)
            ->when($indicator_ids, fn($query)=> $query->whereIn('id', $indicator_ids))
            ->take(20)
            ->get();

    }

    /**
     * 
     * Scores keyword search results using the RRF formula
     * 
     */

    public static function scoreKeywordSearchResults(Collection $keyword_search_results):Collection{

        return $keyword_search_results
                ->map(function($indicator, $index) {

                    return [
                        'indicator' => $indicator,
                        'keyword_rank' => $index + 1,
                        'keyword_score' => 1 / ($index + 1 + 60)
                    ];

                })
                ->keyBy('indicator.id');
    }


    /**
     * 
     * Scores semantic search results using the RRF formula
     * 
     */

    public static function scoreSemanticSearchResults(Collection $semantic_search_results):Collection{

        return  $semantic_search_results
                    ->map(function($indicator, $index) {
                            return [
                                'indicator' => $indicator,
                                'semantic_rank' => $index + 1,
                                'semantic_score' => 1 / ($index + 1 + 60)
                            ];
                        })
                    ->keyBy('indicator.id');
    }


    /**
     * 
     * Merges and ranks the semantic and keyword search
     * 
     * @param $keyword_search_scored A collection of indicator keyword search results and their rff scores. Use the keyword search results scoring method to get the rff score
     * 
     * @param $semantic_search_scored A collection of indicator keyword search results and their rff scores. Use the semantic search results scoring method to get the rff score
     * 
     * @return Illuminate\Support\Collection
     * 
     */

    public static function rankSearchResults(Collection $keyword_search_scored, Collection $semantic_search_scored){

        $merged = [];

        foreach($keyword_search_scored as $id => $data) {
            
            $merged[$id] = [
                'indicator' => $data['indicator'],
                'rrf_score' => $data['keyword_score']
            ];

        }

        foreach($semantic_search_scored as $id => $data) {
            
            //if indicator shows up in the keyword search, add the semantic score to it so it shows up higher in the results
            if(isset($merged[$id])) {

                $merged[$id]['rrf_score'] += $data['semantic_score'];

            } else {

                $merged[$id] = [
                    'indicator' => $data['indicator'],
                    'rrf_score' => $data['semantic_score']
                ];

            }

        }

        // Sort by combined score and take top 10
        return collect($merged)
            ->sortByDesc('rrf_score')
            ->take(10)
            ->pluck('indicator');
    
    }


}