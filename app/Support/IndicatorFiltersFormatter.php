<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class IndicatorFiltersFormatter{


    protected const VALID_OPERATORS = ['eq', 'neq', 'gt', 'gte', 'lt', 'lte', 'in', 'nin', 'null', 'notnull'];

    protected static function resolveTimeframeFilter($merged_filters, $timeframe_filters, $request_filters, $excluded_default_filters, $selected_defaults):array{

        // set generic default filters
    
        $last_index = count($timeframe_filters) - 1;

        $merged_filters['timeframe'] = ['eq' => $timeframe_filters[$last_index]];


        //check if timeframe has a selected. if it does, override the generic filter

        $timeframe_has_selected_default = isset($selected_defaults['timeframe']);

        if($timeframe_has_selected_default) {

            $merged_filters['timeframe'] = ['eq' => $selected_defaults['timeframe']];

        }

        //checks if timeframe is excluded

        $timeframe_is_excluded = in_array('timeframe', $excluded_default_filters);

        if($timeframe_is_excluded){

            $merged_filters = array_filter($merged_filters, fn($key)=> $key !== 'timeframe', ARRAY_FILTER_USE_KEY);

        }

        //finally, checks if filter is in request. if so, adds request filter back in
        $timeframe_in_request = isset($request_filters['timeframe']);

        if($timeframe_in_request){

            $merged_filters['timeframe'] = $request_filters['timeframe'];

            $merged_filters;
        }

        return $merged_filters;

    }

    protected static function resolveFormatFilter($merged_filters, $format_filters, $request_filters, $excluded_default_filters, $selected_defaults):array{

        //generic default
        $first_format = $format_filters->first();

        $merged_filters['format'] = ['eq' => $first_format->id];

        //check if it has selected default filter. if so, override generic

        $format_has_default_filter = isset($selected_defaults['format']);

        if($format_has_default_filter) {

            $merged_filters['format'] = ['eq' => $selected_defaults['format']];

        }

        //see if filter is excluded
        $format_is_excluded = in_array('format', $excluded_default_filters);

        if($format_is_excluded){

            $merged_filters = array_filter($merged_filters, fn($key)=> $key !== 'format');

        }

        //see if filter is in request. if so, add it in
        $format_is_in_request = isset($request_filters['format']);

        if($format_is_in_request){

            $merged_filters['format'] = $request_filters['format'];

        }
        
        return $merged_filters;
    }

    protected static function resolveBreakdownFilter($merged_filters, $breakdown_filters, $request_filters, $excluded_default_filters, $selected_defaults):array{


        //add in generic default filters
        $first_breakdown = $breakdown_filters->first();

        $merged_filters['breakdown_parent'] = ['eq' => $first_breakdown->id];
        
        if(!$first_breakdown->subBreakdowns->isEmpty()) {

            $merged_filters['breakdown'] = ['eq' => $first_breakdown->subBreakdowns->first()->id];

        } else {

            $merged_filters['breakdown'] = ['eq' => $first_breakdown->id];

        }
            
        //check if a breakdown or breakdown parent filters have a selected default stored in the db to override generic default filters
        $default_breakdown_parent_selected = isset($selected_defaults['breakdown_parent']);

        $default_breakdown_selected = isset($selected_defaults['breakdown']);

        if($default_breakdown_parent_selected){

            $merged_filters['breakdown_parent'] = $selected_defaults['breakdown_parent'];

            if(!$default_breakdown_selected){

                $merged_filters['breakdown'] = ['eq' => $selected_defaults['breakdown']];

            }
        }

        if($default_breakdown_selected){

            $merged_filters['breakdown'] = ['eq' => $selected_defaults['breakdown']];

            if(!$default_breakdown_parent_selected){

                $breakdown_id = $merged_filters['breakdown']['eq'];

                $current_breakdown_parent = $breakdown_filters->filter(fn($breakdown)=>$breakdown->subBreakdowns->contains('id', $breakdown_id))->first();

                if(!$current_breakdown_parent){

                    $merged_filters['breakdown_parent'] = ['eq' => $breakdown_id];

                } else {

                    $merged_filters['breakdown_parent'] =  ['eq' => $current_breakdown_parent->first()->id];

                }

            }
        }

        //check for exclusion and remove filter element from array
        $breakdown_is_excluded = in_array('breakdown', $excluded_default_filters);

        $breakdown_parent_is_excluded = in_array('breakdown_parent', $excluded_default_filters);

        if($breakdown_is_excluded){

            $merged_filters = array_filter($merged_filters, fn($key)=>$key !== 'breakdown', ARRAY_FILTER_USE_KEY);
        }

        if($breakdown_parent_is_excluded){

            $merged_filters = array_filter($merged_filters, fn($key)=>$key !== 'breakdown_parent', ARRAY_FILTER_USE_KEY);
        }

        //finally, check if breakdown or breakdown parent filter is in the request to override all filters

        $breakdown_filter_in_request = isset($request_filters['breakdown']);

        $breakdown_parent_filter_in_request = isset($request_filters['breakdown_parent']);

        if($breakdown_parent_filter_in_request){

            $merged_filters['breakdown_parent'] = $request_filters['breakdown_parent'];

        }

        if($breakdown_filter_in_request){

            $merged_filters['breakdown'] = $request_filters['breakdown'];
            
        }
        
        return $merged_filters;
    }

    protected static function resolveLocationTypeFilter($merged_filters, $location_type_filters, $request_filters, $excluded_default_filters, $selected_defaults):array{
        
        //add generic filters
        $first_location_type = $location_type_filters->first();

        $merged_filters['location_type'] = ['eq' => $first_location_type->id];

        $merged_filters['location'] = ['eq' => $first_location_type->locations->first()->id];

        //check if location or location type have selected defaults

        $location_has_selected_default = isset($selected_defaults['location']);

        $location_type_has_selected_default = isset($selected_defaults['location_type']);

        if($location_has_selected_default){

            $merged_filters['location'] = ['eq' => $selected_defaults['location']];
            
        }

        if($location_type_has_selected_default){

            $merged_filters['location_type'] = ['eq' => $selected_defaults['location_type']];

            //if location does not have a selected default, add the first one from the current location type
            if(!$location_has_selected_default){

                $location_type_id = $merged_filters['location_type']['eq'];

                $current_location_type = $location_type_filters->where('id', $location_type_id)->first();
                
                if($current_location_type){

                    $merged_filters['location'] = ['eq' => $current_location_type->locations->first()->id];

                }

                
            }
            
        }

        //check if location and location type is excluded. If so, remove it

        $location_is_excluded = in_array('location', $excluded_default_filters);

        $location_type_is_excluded = in_array('location_type', $excluded_default_filters);

        if($location_is_excluded){

            $merged_filters = array_filter($merged_filters, fn($key)=>$key !== 'location', ARRAY_FILTER_USE_KEY);

        }

        if($location_type_is_excluded){

            $merged_filters = array_filter($merged_filters, fn($key)=>$key !== 'location_type', ARRAY_FILTER_USE_KEY);
            
        }

        //lastly, check if location and location type is included in the request. if so override.

        $location_in_request = isset($request_filters['location']);

        $location_type_in_request = isset($request_filters['location_type']);

        if($location_in_request){

            $merged_filters['location'] = $request_filters['location'];
        }

        if($location_type_in_request){

            $merged_filters['location_type'] = $request_filters['location_type'];

            $merged_filters = self::handleLocationTypeRequest($merged_filters, $request_filters, $location_type_filters, $excluded_default_filters);

        }

        return $merged_filters;
    }

    /**
     * 
     * Handles assigning a location filter when only location type is present in the request
     *
     */
    protected static function handleLocationTypeRequest($merged_filters, array $request_filters, Collection $location_type_collection, $excluded_default_filters){
        
        //check if location is in request filter. If so exit early because there's no need to run the below code

        $location_is_in_request = isset($request_filters['location']);

        if($location_is_in_request){

            return $merged_filters;

        }

        $location_is_excluded = in_array('location', $excluded_default_filters);

        if($location_is_excluded){

            return $merged_filters;

        }
        
        $location_type_filter_value = array_values($request_filters['location_type'])[0];
        
        if (is_array($location_type_filter_value)) {

            $merged_filters = array_filter($merged_filters, fn($key)=>$key !== 'location', ARRAY_FILTER_USE_KEY);

            return $merged_filters;

        }
        
        $location_type = $location_type_collection
            ->where('id', $location_type_filter_value)
            ->first();
        
        $merged_filters['location'] = ['eq' => $location_type->locations->first()->id];
        
        return $merged_filters;
    }

    /**
     * 
     * Merges the default filters with filters passed from the request. 
     * If a filter is not provided in the request, it will use 
     * the default value from the indicator filters. 
     * 
     * @param array $indicator_filters An array of available of filters 
     * for an indicator made up of collections of timeframes, breakdowns, 
     * location_types(and their locations locations), and data_formats
     * 
     * @param array $request_filters The filters provided in the request.
     * 
     * @param array $excluded_default_filters Add filters that should not be filtered by default. 
     * For example if you want to send all years worth of data to the front, add timeframe to the exlusion array
     * 
     * @param array<string, int> $selected_default_filters an array of default filters assigned by admin to be used by default. The key is 
     * the filter_type and value is the filter value
     * 
     * @return array The merged filters
     */

    public static function mergeWithDefaultFilters(
        array $indicator_filters, 
        array $request_filters,
        array $excluded_default_filters = [],
        array $selected_default_filters = []
    ): array {
        
        $merged_filters = [];

        if(isset($indicator_filters['timeframe'])){

            $merged_filters = self::resolveTimeframeFilter(
                    $merged_filters,
                    $indicator_filters['timeframe'], 
                    $request_filters, 
                    $excluded_default_filters, 
                    $selected_default_filters
                );
        }
        

        if(isset($indicator_filters['breakdown'])){
        
            $merged_filters = self::resolveBreakdownFilter(
                    $merged_filters,
                    $indicator_filters['breakdown'], 
                    $request_filters, 
                    $excluded_default_filters, 
                    $selected_default_filters
                );

        }

        if(isset($indicator_filters['location_type'])){

                $merged_filters = self::resolveLocationTypeFilter(
                        $merged_filters,
                        $indicator_filters['location_type'], 
                        $request_filters, 
                        $excluded_default_filters, 
                        $selected_default_filters
                );

        }
        
        if(isset($indicator_filters['format'])){

            $merged_filters = self::resolveFormatFilter(
                $merged_filters,
                $indicator_filters['format'], 
                $request_filters, 
                $excluded_default_filters, 
                $selected_default_filters
            );

        } 

        return $merged_filters;
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