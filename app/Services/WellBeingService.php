<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Location;
use App\Models\WellBeingRanking;
use Illuminate\Database\Eloquent\Collection;

class WellBeingService {


    public static function queryDomains(){

        $domains = Domain::where('is_rankable', true)->get();


        $overall = new Domain([
            'id' => 0,
            'name' => 'Overall',
        ]);

        return $domains->prepend($overall);
    
    }

    public static function queryLocationDomainScore(int $location_id, array $filters): Collection {

        $location_type = Location::where('id', $location_id)->select('location_type_id')->first();

        $locations = Location::where('location_type_id', $location_type->location_type_id)
            ->withRankings($filters)
            ->get();
            
        $locations_sorted_by_score = $locations
            ->sortByDesc(fn($location)=>$location->rankings->first()->score)
            ->values();
        
        $locations_with_rank = $locations_sorted_by_score->each(fn($location, $index)=>$location->rank = $index + 1);
        
        return $locations_with_rank;

    }


}