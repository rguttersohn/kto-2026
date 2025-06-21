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
            'slug' => $filters_array['slug'],
            'data' => [
                'timeframes' => $filter_ids_array['timeframes'],
                'breakdowns' => Breakdown::select('name', 'slug', 'id')
                    ->whereIn('id', $filter_ids_array['breakdowns'])
                    ->with('subBreakdowns:id,name,parent_id')
                        ->get()->toArray(),
                'location_types' => LocationType::defaultSelects()
                    ->whereIn('id', $filter_ids_array['location_types'])
                    ->get()->toArray(),
                'data_formats' => DataFormat::select('name', 'id')->whereIn('id', $filter_ids_array['data_formats'])->get()->toArray()
            ]
            ];
    }

}