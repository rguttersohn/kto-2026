<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\Indicator;
use Illuminate\Database\Eloquent\Model;
use App\Models\IndicatorData;
use Illuminate\Support\Facades\Cache;
use Exception;

class IndicatorService {

    public static function queryAllIndicators():Collection{

        return Indicator::all();
    }

    public static function queryIndicator($indicator_id):Model | null{

        return Indicator::select('id', 'name', 'definition','note', 'source')
            ->where('id', $indicator_id)
            ->first();
        
    }

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


}