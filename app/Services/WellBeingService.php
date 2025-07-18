<?php

namespace App\Services;

use App\Models\Domain;


class WellBeingService {


    public static function queryDomains(){

        $domains = Domain::where('is_rankable', true)->get();


        $overall = new Domain([
            'id' => 0,
            'name' => 'Overall',
        ]);

        return $domains->prepend($overall);
    
    }


}