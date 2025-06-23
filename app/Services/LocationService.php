<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Models\LocationType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;

class LocationService {

    public static function queryAllLocationTypes():Collection{

        return LocationType::select('id', 'name', 'plural_name','slug','scope', 'classification')->get();

    }

    public static function queryLocation(int $location_id):Model{
        return Location::select('location_type_id','name','id','fips','geopolitical_id')
        ->where('id', $location_id)
        ->first();
    }
}