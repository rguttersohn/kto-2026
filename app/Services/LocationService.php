<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\LocationType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Support\PostGIS;

class LocationService {

    public static function queryAllLocationTypes(array | null $location_type_ids):Collection{

        return LocationType::when($location_type_ids, fn($query)=>$query->whereIn('id', $location_type_ids))
            ->get();

    }

    public static function queryLocationsByLocationType(int $location_type_id, ?bool $wants_geojson = false):Collection | null{
        return Location::select('location_type_id','name','locations.id','fips','district_id')
        ->where('location_type_id', $location_type_id)
        ->when($wants_geojson, function($query){

            $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'));

        })
        ->get();
    }


    public static function queryLocation(int $location_type_id, int $location_id, ?bool $wants_geojson = false):Model | null{
        
        return Location::select('locations.location_type_id','locations.name','locations.id','locations.fips','locations.district_id')
            ->where([['locations.id', $location_id], ['locations.location_type_id', $location_type_id]])
            ->when($wants_geojson, function($query){

                $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'));

            })
            ->first();
    }

    public static function queryIsLocationTypeRanked(int $location_type_id): bool{

        $location_type = LocationType::where('id', $location_type_id)->first();

        return $location_type->has_ranking;
    }

    public static function queryLocationTypeWithLocation($location_type_id, bool $wants_geojson):Model{

        return LocationType::select('id', 'name', 'plural_name','scope', 'classification')
            ->with(['locations' => function($query)use($wants_geojson){
                $query->select('location_type_id', 'name','locations.id','fips','district_id')
                    ->when($wants_geojson, function($query){
                        $query->join('locations.geometries as geo', 'locations.id', 'geo.location_id')
                            ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry'));
                    });
            }])
            ->where('id', $location_type_id)
            ->first();
    }

    public static function queryLocationTypeIndicators(LocationType $location_type):LocationType{

        return $location_type->load('indicators');

    }
}