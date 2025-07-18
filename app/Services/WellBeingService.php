<?php

namespace App\Services;

use App\Models\Domain;


class WellBeingService {


    public static function queryDomains(){

        return Domain::where('is_rankable', true)->get();
    
    }


}