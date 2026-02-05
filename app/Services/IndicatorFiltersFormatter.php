<?php

namespace App\Services;

use Illuminate\Support\Str;

class IndicatorFiltersFormatter{


    protected const VALID_OPERATORS = ['eq', 'neq', 'gt', 'gte', 'lt', 'lte', 'in', 'nin', 'null', 'notnull'];

    protected static function resolveTimeframeFilter($value, $selected_defaults){
                        

            if(isset($selected_defaults['timeframe'])) {

                return ['eq' => $selected_defaults['timeframe']];

            }
            
            $last_index = count($value) - 1;

            return ['eq' => $value[$last_index]];

        }

    protected static function resolveBreakdownFilter($value, $selected_defaults){
            
            
            if(isset($selected_defaults['breakdown'])) {
            
                return ['eq' => $selected_defaults['breakdown']];

            }
            
            $first_breakdown = $value->first();
            
            if(!$first_breakdown->subBreakdowns->isEmpty()) {

                return ['eq' => $first_breakdown->subBreakdowns->first()->id];

            }
            
            return ['eq' => $first_breakdown->id];
    }

    protected static function resolveLocationTypeFilter($value, $selected_defaults, $exclude_defaults){
        
        $filters = [];
        
        // Resolve location_type
        if (isset($selected_defaults['location_type'])) {

            $filters['location_type'] = ['eq' => $selected_defaults['location_type']];

        } else {

            $filters['location_type'] = ['eq' => $value->first()->id];

        }
        
        // Resolve location if not excluded

        $filter_is_excluded = in_array('location', $exclude_defaults);

        if (!$filter_is_excluded) {

            if (isset($selected_defaults['location'])) {

                $filters['location'] = ['eq' => $selected_defaults['location']];

            } else {

                $locations = $value->where('id', $filters['location_type']['eq'])->first()->locations;
                $filters['location'] = ['eq' => $locations->first()->id];

            }
        }
        
        return $filters;
    }

    protected static function resolveFormatFilter($value, $selected_defaults){
        

        if (isset($selected_defaults['format'])) {

            return ['eq' => $selected_defaults['format']];

        }
        
        return ['eq' => $value->first()->id];
    }


    protected static function getFilterResolver($key){
        
        return match($key) {
            'timeframe' => fn($value, $defaults, $exclude) => 
                self::resolveTimeframeFilter($value, $defaults),
            'breakdown' => fn($value, $defaults, $exclude) => 
                self::resolveBreakdownFilter($value, $defaults),
            'location_type' => fn($value, $defaults, $exclude) => 
                self::resolveLocationTypeFilter($value, $defaults, $exclude),
            'format' => fn($value, $defaults, $exclude) => 
                self::resolveFormatFilter($value, $defaults),
            default => fn($value, $defaults, $exclude) => 
                ['eq' => $value->first()->id]
        };
        
    }

    protected static function handleLocationTypeRequest($request_filters, $location_type_collection){
        
        $result = [];
        
        if (isset($request_filters['location'])) {

            $result['location'] = $request_filters['location'];

            return $result;
        }
        
        $location_type_filter_value = array_values($request_filters['location_type'])[0];
        
        if (is_array($location_type_filter_value)) {

            return $result;

        }
        
        $location_type = $location_type_collection
            ->where('id', $location_type_filter_value)
            ->first();
        
        $result['location'] = ['eq' => $location_type->locations->first()->id];
        
        return $result;
    }


    /**
     * 
     * Merges the default filters with the provided filters. If a filter is not provided in the request, it will use the default value from the indicator filters. Note: This is useful for rendering data on a map
     * 
     * @param array $indicator_filters An array of collections for timeframes, breakdowns, location_types with locations, and data_formats
     * 
     * @param array $request_filters The filters provided in the request.
     * 
     * @param array $exclude_defaults Add filters that should not be filtered by default. For example if you want to send all years worth of data to the front, add timeframe to the exlusion array
     * 
     * @param array<string, int> $selected_default_filters an array of default filters where the key is the filter_type and value is the filter value
     * 
     * @return array The merged filters
     */

    public static function mergeWithDefaultFilters(
        array $indicator_filters, 
        array $request_filters,
        array $exclude_defaults = [],
        array $selected_default_filters = []
    ): array {
        
        $filters = [];
        
        foreach ($indicator_filters as $key => $value) {
            
            // Handle request filters first
            $has_filter_in_request = isset($request_filters[$key]);

            if ($has_filter_in_request) {
                
                $filters[$key] = $request_filters[$key];
                
                // Special handling for location_type with location
                if ($key === 'location_type') {

                    $filters = array_merge(
                        $filters, 
                        self::handleLocationTypeRequest($request_filters, $value)
                    );

                }
                
                continue;
            }
            
            // Skip if excluded

            $is_excluded = in_array($key, $exclude_defaults);

            if ($is_excluded) {
                
                continue;

            }
            
            // Apply default filter resolution
            $resolver = self::getFilterResolver($key);
            $resolved = $resolver($value, $selected_default_filters, $exclude_defaults);
            
            $first_key = array_key_first($resolved);
            $is_operator = self::isValidOperator($first_key);

            if ($is_operator) {
                // Single filter with operator
                $filters[$key] = $resolved;
            } else {
                // Multiple filters
                $filters = array_merge($filters, $resolved);
            }
        
        }
        
        return $filters;
    }

    public static function toSelectedFilters(array $filters, array $availableFilters): array
    {
        $selectedFilters = [];

        foreach ($filters as $name => $conditions) {
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

    protected static function isValidOperator(string $key): bool {
        
        return in_array($key, self::VALID_OPERATORS);
        
    }

    protected static function getFilterLabel(string $name): ?string
    {
        return match ($name) {
            'timeframe' => 'Timeframe',
            'location_type' => 'Location Type',
            'location' => 'Location',
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
                    
                    
                    if($group['subBreakdowns']->isEmpty()){
                        
                        
                        if ((string) $group['id'] === (string) $value) {
                            
                            return $group['name'];

                        }
                         
                    }

                
                    foreach ($group['subBreakdowns'] ?? [] as $sub) {
                        
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

            case 'location':
                
                foreach ($availableFilters['location_type'] ?? [] as $loc) {

                    $locations = $loc->locations->where('location_type_id', $loc->id);
                
                    foreach($locations as $location){
                        
                        if ((string) $location['id'] === (string) $value) {
                            
                            return $location['name'];

                        }
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