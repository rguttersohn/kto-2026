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

    public static function toSelectedFilters(array $requestFilters, array $availableFilters): array
    {
        $selectedFilters = [];

        foreach ($requestFilters as $name => $conditions) {
            foreach ($conditions as $operator => $value) {
                $selectedFilters[] = [
                    'id' => (string) Str::uuid(),
                    'filterName' => [
                        'label' => self::getFilterLabel($name, $value, $availableFilters),
                        'value' => $name,
                    ],
                    'operator' => [
                        'label' => self::getOperatorLabel($operator),
                        'value' => $operator,
                    ],
                    'value' => [
                        'label' => self::getValueLabel($name, $value, $availableFilters),
                        'value' => $value,
                    ],
                ];
            }
        }

        return $selectedFilters;
    }

    protected static function getOperatorLabel(string $operator): string
    {
        return match ($operator) {
            'eq' => 'Equals',
            'neq' => 'Not equal to',
            'gt' => 'Greater than',
            'gte' => 'Greater than or equal to',
            'lt' => 'Less than',
            'lte' => 'Less than or equal to',
            'in' => 'In list',
            'nin' => 'Not in list',
            'null' => 'Is null',
            'notnull' => 'Is not null',
            default => ucfirst($operator),
        };
    }

    protected static function getFilterLabel(string $name): ?string
    {
        return match ($name) {
            'timeframe' => 'Timeframe',
            'location_type' => 'Location Type',
            'format' => 'Format',
            'breakdown' => 'Breakdown',
            default => ucfirst(str_replace('_', ' ', $name)),
        };
    }

    protected static function getValueLabel(string $name, mixed $value, array $availableFilters): string|array|null
    {
        
        if (is_array($value)) {
            return array_map(
                fn($v) => self::resolveValueLabel($name, $v, $availableFilters),
                $value
            );
        }

        return self::resolveValueLabel($name, $value, $availableFilters);
    }

    protected static function resolveValueLabel(string $name, mixed $value, array $availableFilters): string|null
    {
        switch ($name) {
            case 'breakdown':
                foreach ($availableFilters['breakdown'] ?? [] as $group) {
                    foreach ($group['sub_breakdowns'] ?? [] as $sub) {
                        if ((string) $sub['id'] === (string) $value) {
                            return $sub['name'];
                        }
                    }
                }
                break;

            case 'location_type':
                foreach ($availableFilters['location_type'] ?? [] as $loc) {
                    if ((string) $loc['id'] === (string) $value) {
                        return $loc['plural_name'] ?? $loc['name'];
                    }
                }
                break;

            case 'format':
                foreach ($availableFilters['format'] ?? [] as $fmt) {
                    if ((string) $fmt['id'] === (string) $value) {
                        return $fmt['name'];
                    }
                }
                break;

            case 'timeframe':
                return (string) $value;
        }

        return (string) $value;
    }



}