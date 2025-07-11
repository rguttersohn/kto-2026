<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait SpatialQueryable {

    /**
     * Scope to filter results where a geometry column is within user-submitted GeoJSON.
     *
     * @param Builder $query
     * @param string $column The geometry column (e.g., 'location')
     * @param array $geojson The user-submitted GeoJSON as an associative array
     * @return Builder
     */

    #[Scope]
    protected function isGeometryWithinGeoJSON(Builder $query, string $column, array $geojson){

        return $query->whereRaw(
            "ST_Within($column, ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)) = true",
            [json_encode($geojson)]
        );

    }

}