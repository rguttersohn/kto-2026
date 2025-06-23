<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Indicator;
use Illuminate\Database\Eloquent\Model;

class IndicatorService {

    public static function queryAllIndicators():Collection{

        return Indicator::select('id', 'name', 'slug')->get();
    }

    public static function queryIndicator($indicator_id):Model{

        return Indicator::select('id', 'name', 'slug', 'definition','note', 'source')
            ->where('id', $indicator_id)
            ->first();
        
    }

    public static function queryIndicatorWithData($indicator_id, $limit, $offset, $wants_geojson, $filters, $sorts):Model{
       
        return Indicator::select('id', 'name', 'slug', 'definition','note', 'source')
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


    public static function queryIndicatorFilters($indicator_id):Model{
        
        return Indicator::select('id', 'name', 'slug')
            ->withAvailableFilters()
            ->where('id', $indicator_id)
            ->first();
            
        
    }


}