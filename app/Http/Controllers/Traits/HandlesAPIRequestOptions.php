<?php

namespace App\Http\Controllers\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\ValidFilterOperator;
use App\Rules\ValidFilterOperatorStructure;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\FormatTypes;


trait HandlesAPIRequestOptions
{
    
    
    protected function as(Request $request){

        $as = $request->has('as') ? $request->as : 'json';
        
        $validator = Validator::make(
            ['as' => $as],
            [
            'as' => ['required','string', new Enum(FormatTypes::class)]
            ]
        );

        if($validator->fails()){

            throw new ValidationException($validator);

        }

        return $as;

    }


    protected function wantsGeoJSON(Request $request): bool
    {
        
        $as = $this->as($request);

        $wants_geojson = false;

        $accepts_geojson = str_contains($request->header('Accept'), 'application/geo+json');

        if ($as === 'geojson' || $accepts_geojson) {
            $wants_geojson = true;
        }

        return $wants_geojson;

    }

    protected function wantsCSV(Request $request):bool {

        $as = $this->as($request);

        $wants_csv = false;

        if ($as === 'csv' || str_contains($request->header('Accept'), 'text/csv')) {
            
            $wants_csv = true;
        
        }

        return $wants_csv;
    
    }


    protected function subcategory(Request $request): null | int | array {

        $subcategory = $request->has('subcategory') ? $request->subcategory : null;

        $validator = Validator::make(
            ['subcategory' => $subcategory],
            [
                'subcategory' => [
                    'nullable',
                    function($attribute, $value, $fail){
                    
        
                        if (is_array($value)) {
                            foreach ($value as $item) {
                                
                                if (!is_numeric($item)) {
                                    return $fail("Each $attribute must be an integer.");
                                }
                            }
                        } elseif (!is_numeric($value)) {
                            return $fail("The $attribute must be an integer.");
                        }

                    }
                ]
            ]);

        if ($validator->fails()) {
            return null;
        } 
        
        if(is_numeric($subcategory)){
            return (int) $subcategory;
        }

        if(is_array($subcategory)){
            return (array) $subcategory;
        }

        return $subcategory;
                
            
    }

    protected function filters(Request $request): array{

        $filters = $request->input('filter', []);
        
        $validator = Validator::make(
            ['filter' => $filters],
            [
                'filter' => ['array'],
                'filter.*' => ['array'],
                'filter.*' => [new ValidFilterOperatorStructure],
                'filter.*.*' => [new ValidFilterOperator],
            ]
        );

        if($validator->fails()){

            throw new ValidationException($validator);

        }

        return $filters;


    }

    protected function sorts(Request $request): array {
        
        $sorts = $request->input('sort', []);

        $validator = Validator::make(
            ['sort' => $sorts],
            [
                'sort' => ['array'],
                'sort.*' => ['in:asc,desc'],
            ]
        );

        if ($validator->fails()) {

            throw new ValidationException($validator);

        }


        return $sorts;

    }

    protected function limit(Request $request, int $max = 3000): int
    {
        $limit = $request->query('limit');

        $validator = Validator::make(
            ['limit' => $limit],
            [
                'limit' => ['nullable', 'integer', 'min:1', 'max:' . $max],
            ]
        );

        if ($validator->fails()) {

            throw new ValidationException($validator);

        }

        return isset($limit) ? (int) $limit : $max;
    }


    protected function offset(Request $request): int
    {
        $offset = $request->query('offset');

        $validator = Validator::make(
            ['offset' => $offset],
            [
                'offset' => ['nullable', 'integer', 'min:0'],
            ]
        );

        if ($validator->fails()) {
            
            throw new ValidationException($validator);

        }

        return isset($offset) ? (int) $offset : 0;
    }

    /**
     * 
     * Determines if the request wants to merge default filters with the provided filters.
     * 
     * @param Request $request
     * 
     * @return bool|ValidationException
     * 
     */

    protected function wantsMergeDefaultFilters(Request $request):bool {
        
        $merge_defaults = $request->query('merge-default-filters');

        $validator = Validator::make(
            ['merge-defaults' => $merge_defaults],
            [
                'merge_defaults' => ['boolean']
            ]
        );
        
        if ($validator->fails()) {
            
            throw new ValidationException($validator);

        }

        return isset($merge_defaults) ? $merge_defaults : false;
    }


    /**
     * 
     * Checks for 'excluded-default-filters' param and validates list of filters that should not have a default filter returned
     * 
     * @param Request $request
     * 
     * @return array
     * 
     * 
     */

    protected function excludedDefaultFilters(Request $request): array{
        
        if (!$request->has('merge-default-filters') || !$request->boolean('merge-default-filters')) {
            
            return [];
            
        }
        
        if(!$request->has('excluded-default-filters')){

            return [];
        }
        
        $excluded_default_filters = $request->query('excluded-default-filters');

        $validator = Validator::make(
            ['excluded-default-filter' => $excluded_default_filters],
            ['excluded-default-filter' => ['array']]
        );

        if($validator->fails()){

            throw new ValidationException($validator);
        }

        return $excluded_default_filters;
    }


    /**
     * 
     * Handles and validates the submission of geojson
     * 
     */
    protected function geometry(Request $request):array | null {

        $geometry = $request->all()['geometry'] ?? null;

        if(!$geometry){

            return null;
        };

        $validator = Validator::make(
            ['geometry' => $geometry],
            [
                'geometry.type' => ['required', 'string', Rule::in([
                    'Point', 'LineString', 'Polygon', 'MultiPoint', 'MultiLineString', 'MultiPolygon', 'GeometryCollection'
                ])],
                'geometry.coordinates' => ['required', 'array'],
            ]
        );


        if($validator->fails()){

            throw new ValidationException($validator);

        }

        return $geometry;

    }

    /**
     * 
     * handles and validates if q param is present. Used for endpoints that handle searching
     * 
     */

    protected function q(Request $request):string | null{

        if(!$request->has('q')){

            return null;

        }

        $q = $request->q;

        $validator = Validator::make(
            ['q' => $q],
            ['q' => ['string']]
        );

        if($validator->fails()){

            throw new ValidationException($validator);

        }

        return $q;
    
    }
    
}