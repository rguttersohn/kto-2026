<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\MultiPolygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Enums\Srid;
use MatanYadaev\EloquentSpatial\Objects\Point;

class PostGIS {


    public static function isGeometryWithin(string $inner_geometry, string $outer_geometry):array{

        return [DB::raw("ST_Within($inner_geometry, $outer_geometry)"), '=', DB::raw('true')];
    }

    

    public static function getLongLatFromPoint(string $table, string $geometry_column){

        return ("ST_X($table.$geometry_column) as longitude, ST_Y($table.$geometry_column) as latitude");
    }

   
    public static function getSimplifiedGeoJSON(string $table, string $geometry_column, float $tolerance):string{

        return ("St_asgeojson(ST_simplify($table.$geometry_column, $tolerance)) as $geometry_column");
    }

    public static function getGeoFromText(string $wkt_text):string{

        return ("ST_SetSRID(ST_GeomFromText($wkt_text),4326)");
    }

    public static function getGeoJSON(string $table, string $geometry_column):string{
        
        return ("ST_asgeojson($table.$geometry_column) as $geometry_column");
    }


    public static function createPoint (array $coordinates){

        $point = new Point($coordinates[1],$coordinates[0], Srid::WGS84->value);

        return $point;
    }

    public static function createPolygon (array $coordinates){

        $polygon = new Polygon(
            array_map(function($coordinate){
                return new LineString(
                    array_map(function($points){
                        return new Point($points[1],$points[0]);
                    }, $coordinate)
                );
            }, $coordinates),
            Srid::WGS84->value
        );

        return $polygon;
    }

    public static function createMultiPolygon (array $polygons){

        $multipolygon = new MultiPolygon(
            array_map(function($polygon){
                return new Polygon(
                    array_map(function($coordinates){
                        return new LineString(
                            array_map(function($points){
                                return new Point($points[1],$points[0], Srid::WGS84->value);
                            }, $coordinates)
                        );
                    }, $polygon),
                    Srid::WGS84->value
                );
            },$polygons),
            Srid::WGS84->value
        );

        return $multipolygon;
        
    }

}