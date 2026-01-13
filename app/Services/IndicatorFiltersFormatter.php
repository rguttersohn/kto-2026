<?php

namespace App\Services;

use Illuminate\Support\Str;

class IndicatorFiltersFormatter{


    /**
     * 
     * Merges the default filters with the provided filters. If a filter is not provided in the request, it will use the default value from the indicator filters. Note: This is useful for rendering data on a map
     * 
     * @param array $indicator_filters An array of collections for timeframes, breakdowns, location_types, data_formats
     * 
     * @param array $request_filters The filters provided in the request.
     * 
     * @param array $exclude_defaults Add filters that should not be filtered by default. For example if you want to send all years worth of data to the front, add timeframe to the exlusion array
     * 
     * @return array The merged filters
     */

    public static function mergeWithDefaultFilters(
        array $indicator_filters, 
        array $request_filters,
        array $exclude_defaults = []
        ):array{

        $filters = [];

        foreach ($indicator_filters as $key => $value) {
            
            $filter_is_included_in_request = isset($request_filters[$key]);

            if ($filter_is_included_in_request){
                
                $filters[$key] = $request_filters[$key];

                continue;
            
            }

            $filter_is_excluded = in_array($key, $exclude_defaults);
            
            if($filter_is_excluded){

                continue;
            }
                
            if($key === 'timeframe'){
                
                $filters['timeframe'] = [
                    'eq' => $value[count($value) - 1]
                ];

                continue;
            }

            if($key === 'breakdown'){
                
                $first_breakdown = $value->first();

                $first_breakdown_has_sub_breakdown = !$first_breakdown->subBreakdowns->isEmpty();

                if($first_breakdown_has_sub_breakdown){
                    
                    $first_sub_breakdown = $first_breakdown->subBreakdowns->first();

                    $filter_value = $first_sub_breakdown->id;

                } else {

                    $filter_value = $first_breakdown->id;
                }

                $filters[$key] = [
                    'eq' => $filter_value
                ];
                
                continue;
            }

            if($key === 'location_type'){

                $filters[$key] = [
                    'eq' => $value->first()->id
                ];
            }

            if($key === 'format'){
                
                $filters[$key] = [
                    'eq' => $value->first()->id
                ];
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