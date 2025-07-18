<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;

class WellBeingService {


    public static function queryDomains(){

        $domains = Domain::where('is_rankable', true)->get();


        $overall = new Domain([
            'id' => 0,
            'name' => 'Overall',
        ]);

        return $domains->prepend($overall);
    
    }

    public static function queryLocationDomainScore(int $location_id, array $filters): Model {

        return Location::where('id', $location_id)->withRankings($filters)->first();

    }


}