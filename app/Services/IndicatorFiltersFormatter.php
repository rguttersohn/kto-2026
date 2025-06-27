<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Breakdown;
use App\Models\LocationType;
use App\Models\DataFormat;

class IndicatorFiltersFormatter{

    public static function formatFilters(Model $filters_unformatted):array{

        $filters_array = $filters_unformatted->toArray();

        $filter_ids_string = $filters_array['data'][0];
        
        $filter_ids_array = array_map(function($ids){

            return Str::of($ids)
                ->trim('{}')
                ->explode(',')
                ->map(fn ($val) => (int) $val)
                ->toArray();

        }, $filter_ids_string);
        

        return [
            'id' => $filters_array['id'],
            'name' => $filters_array['name'],
            'data' => [
                'timeframe' => $filter_ids_array['timeframes'],
                'breakdown' => Breakdown::select('name', 'id')
                    ->whereIn('id', $filter_ids_array['breakdowns'])
                    ->with('subBreakdowns:id,name,parent_id')
                        ->get()->toArray(),
                'location_type' => LocationType::defaultSelects()
                    ->whereIn('id', $filter_ids_array['location_types'])
                    ->get()->toArray(),
                'format' => DataFormat::select('name', 'id')->whereIn('id', $filter_ids_array['data_formats'])->get()->toArray()
            ]
            ];
    }

    

    public static function mergeWithDefaultFilters($indicator_filters, $request_filters):array{
        
        $filters = [];

        foreach ($indicator_filters as $key => $value) {
            
            if (isset($request_filters[$key])) {
                
                $filters[$key] = $request_filters[$key];
            
            } else {

                if($key === 'timeframe'){
                    
                    $filters['timeframe'] = [
                        'eq' => $value[count($value) - 1]
                    ];

                    continue;
                }

                if($key === 'breakdown'){

                    $filters[$key] = [
                        'eq' => $value[0]['sub_breakdowns'][0]['id']
                    ];
                    
                    continue;
                }

                if($key === 'location_type'){

                    $filters[$key] = [
                        'eq' => $value[0]['id']
                    ];
                }

                if($key === 'format'){
                    
                    $filters[$key] = [
                        'eq' => $value[0]['id']
                    ];
                }
            }
        }

        return $filters;

        
    }


}