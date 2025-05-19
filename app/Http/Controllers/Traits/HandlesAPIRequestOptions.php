<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

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
}