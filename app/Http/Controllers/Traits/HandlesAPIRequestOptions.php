<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

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

 
}