<?php

namespace App\Filament\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use App\Enums\IndicatorFilterTypes;

class AdminIndicatorService{

     


     public static function rememberFilter(int $indicator_id, string $filter_name, callable $callback, string | null $additional_key = null):Collection {


        try{

            $case = IndicatorFilterTypes::from($filter_name);

            
        }catch(Exception $error){

            throw new Exception($error->getmessage());

        }

        $filter_name = $case->value;

        $key = "admin_indicator_{$filter_name}_{$indicator_id}";
        
        if($additional_key){


            $key .= "_$additional_key";

        }
        
        return Cache::tags(["admin","indicator_$indicator_id","filters"])
            ->rememberForever($key, $callback);

    }
}