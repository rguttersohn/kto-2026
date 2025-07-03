<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\LocationType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Support\PostGIS;

class LocationService {

    public static function queryAllLocationTypes():Collection{

        return LocationType::select('id', 'name', 'plural_name','scope', 'classification')->get();

    }

    public static function queryLocation(int $location_id):Model | null{
        return Location::select('location_type_id','name','id','fips','geopolitical_id')
        ->where('id', $location_id)
        ->first();
    }

    public static function queryLocationTypeWithLocation($location_type_id, bool $wants_geojson):Model{

        return LocationType::select('id', 'name', 'plural_name','scope', 'classification')
            ->with(['locations' => function($query)use($wants_geojson){
                $query->select('location_type_id', 'name','locations.id','fips','geopolitical_id')
                    ->when($wants_geojson, function($query){
                        $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                            ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry', .0001));
                    });
            }])
            ->where('id', $location_type_id)
            ->first();
    }
}