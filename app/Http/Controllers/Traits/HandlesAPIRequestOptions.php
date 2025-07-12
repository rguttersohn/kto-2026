<?php

namespace App\Http\Controllers\Traits;

use App\Models\Scopes\ValidLocationScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\ValidFilterOperator;
use App\Rules\ValidFilterOperatorStructure;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;


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

    protected function wantsCSV(Request $request):bool {

        $as = $request->has('as') ? $request->as : 'json';

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

    protected function wantsMergeDefaults(Request $request):bool | ValidationException {

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

    protected function locationType(Request $request):int | null | ValidationException{

        if(!$request->has('location_type')){
            return null;
        }
        
        $location_type = $request->query('location_type');

        $validator = Validator::make(
            ['location_type' => $location_type],
            [
                'location_type' => ['integer','min:1']
            ]
        );

        if($validator->fails()){

            return new ValidationException($validator);
        }

        return $location_type;

    }

    protected function location(Request $request):int | null | ValidationException{

        if(!$request->has('location')){
            return null;
        }
        
        $location = $request->query('location');

        $validator = Validator::make(
            ['location_type' => $location],
            [
                'location_type' => ['integer','min:1']
            ]
        );

        if($validator->fails()){

            return new ValidationException($validator);
        }

        return $location;

    }

    protected function indicator(Request $request):int | null | ValidationException{

        
        $indicator = $request->query('indicator');

        if(!$indicator){

            return null;

        }

        $validator = Validator::make(
            ['indicator' => $indicator],
            [
                'indicator' => ['integer','min:1']
            ]
        );

        if($validator->fails()){

            return new ValidationException($validator);
        }

        return $indicator;

    }

    protected function by(Request $request, array $allowed_values = []):string | null | ValidationException {

        if (!$request->has('by')) {
            return null;
        }
    
        $by = $request->query('by');

        $rules = ['string'];

        if (!empty($allowed_values)) {
            $rules[] = Rule::in($allowed_values);
        }
    
        $validator = Validator::make(
            ['by' => $by],
            ['by' => $rules]
        );

        if($validator->fails()){

            return new ValidationException($validator);
        }

        return $by;
        
    }


    protected function geometry(Request $request):array | null | ValidationException{

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

            return new ValidationException($validator);

        }

        return $geometry;

    }

    
}