<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;
use App\Models\LocationType;
use App\Models\WellBeingScore;
use Illuminate\Database\Query\JoinClause;
use App\Support\PostGIS;

class WellBeingService {

    public static function orderByScore(Collection $scores){

        dd($scores);

        return $scores
            ->sortByDesc(fn($score)=>$score->rankings->first()->score)
            ->values();

    }

    public static function insertRank(Collection $sorted_locations){

        return $sorted_locations->each(fn($location, $index)=>$location->rank = $index + 1);

    }

    public static function queryAvailableYears(){

        return WellBeingScore::select('year')->distinct()->get();
    }

    public static function queryDomains(){

        $domains = Domain::where('is_rankable', true)->get();


        $overall = new Domain([
            'id' => 0,
            'name' => 'Overall',
        ]);

        return $domains->prepend($overall);
    
    }

    public static function queryRankableLocationTypes():collection{

        return LocationType::where('has_ranking', true)->get();
    
    }

    public static function queryDomainScoreByLocationType(int $domain_id, int $location_type_id, array $filters, bool $wants_geojson = false): Collection | null {

        $locations = Location::select('id')->where('location_type_id', $location_type_id)
            ->get();

        $scores = WellBeingScore::whereIn('scores.location_id', $locations->pluck('id'))
            ->select('scores.id', 'locations.locations.name', 'scores.location_id','score', 'year')
            ->join('locations.locations', function(JoinClause $join)use($locations){

                $join->on('scores.location_id', 'locations.locations.id')
                    ->whereIn('locations.locations.id', $locations->pluck('id'))
                    ->whereNull('locations.locations.valid_ending_on');
            })
            ->when($wants_geojson, function($query){

                $query->join('locations.geometries', 'locations.locations.id', 'locations.geometries.location_id')
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries', 'geometry'));
            })
            ->where('domain_id', $domain_id)
            ->filter($filters)
            ->get();
        
        $scores_sorted = $scores
                ->sortByDesc(fn($score)=>$score->score)
                ->values();

        $scores_with_rank = $scores_sorted->each(fn($location, $index)=>$location->rank = $index + 1);

        return $scores_with_rank;
    }

    public static function queryLocationDomainScore(int $location_id, array $filters): Collection {

        $location_type = Location::where('id', $location_id)->select('location_type_id')->first();

        $locations = Location::where('location_type_id', $location_type->location_type_id)
            ->withRankings($filters)
            ->get();
            
        $locations_sorted_by_score = self::orderByScore($locations);

        $locations_with_rank = self::insertRank($locations_sorted_by_score);
        
        return $locations_with_rank;

    }


}