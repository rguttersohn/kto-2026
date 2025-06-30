<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\ValidFilterOperator;
use App\Rules\ValidFilterOperatorStructure;
use Illuminate\Validation\ValidationException;


trait HandlesAPIRequestOptions
{
    
    protected function wantsGeoJSON(Request $request): bool
    {
        $as = $request->has('as') ? $request->as : 'json';

        $wants_geojson = false;

        $accepts_geojson = str_contains($request->header('Accept'), 'application/geo+json');

        if ($as === 'geojson' || $accepts_geojson) {
            $wants_geojson = true;
        }

        return $wants_geojson;
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

    protected function filters(Request $request): array | ValidationException{

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

            return new ValidationException($validator);

        }

        return $filters;


    }

    protected function sorts(Request $request): array | ValidationException{
        
        $sorts = $request->input('sort', []);

        $validator = Validator::make(
            ['sort' => $sorts],
            [
                'sort' => ['array'],
                'sort.*' => ['in:asc,desc'],
            ]
        );

        if ($validator->fails()) {

            return new ValidationException($validator);

        }


        return $sorts;

    }

    protected function limit(Request $request, int $max = 3000): int | ValidationException
    {
        $limit = $request->query('limit');

        $validator = Validator::make(
            ['limit' => $limit],
            [
                'limit' => ['nullable', 'integer', 'min:1', 'max:' . $max],
            ]
        );

        if ($validator->fails()) {
            return new ValidationException($validator);
        }

        return isset($limit) ? (int) $limit : $max;
    }


    protected function offset(Request $request): int | ValidationException
    {
        $offset = $request->query('offset');

        $validator = Validator::make(
            ['offset' => $offset],
            [
                'offset' => ['nullable', 'integer', 'min:0'],
            ]
        );

        if ($validator->fails()) {
            
            return new ValidationException($validator);

        }

        return isset($offset) ? (int) $offset : 0;
    }

    protected function mergeDefaults(Request $request):bool | ValidationException {

        $merge_defaults = $request->query('merge-defaults');

        $validator = Validator::make([
            ['merge-defaults', $merge_defaults],
            ],
            [
                'merge_defaults' => ['boolean']
            ]);
        
        if ($validator->fails()) {
            
            return new ValidationException($validator);

        }

        return isset($merge_defaults) ? $merge_defaults : false;
    }

    
}