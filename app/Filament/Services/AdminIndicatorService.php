<?php

namespace App\Filament\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class AdminIndicatorService{

     public static function validateFilterNames(string $filter_name): string | bool {

        return match($filter_name){
                'breakdowns' => 'breakdowns',
                'timeframes' => 'timeframes',
                'locations' => 'locations',
                'imports' => 'imports',
                'formats' => 'formats',
                'location_types' => 'location_types',
                default => false
            };
    }


     public static function rememberFilter(int $indicator_id, string $filter_name, callable $callback):Collection {

        $validated_filter_name = static::validateFilterNames($filter_name);

        if(!$validated_filter_name){

            throw new Exception('Indicator filter name is not valid');
        }
        
        return Cache::tags(["admin","indicator_$indicator_id","filters"])
            ->rememberForever("admin_indicator_{$filter_name}_{$indicator_id}", $callback);

    }
}