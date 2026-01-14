<?php

namespace App\Services;

use App\Models\Domain;

class DomainService {

    public static function queryDomains(array | null $domain_ids = null){

        return Domain::when($domain_ids, fn($query)=>$query->whereIn('id', $domain_ids))->get();
    
    }

}