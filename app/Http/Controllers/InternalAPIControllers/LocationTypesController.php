<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Models\LocationType;
use App\Http\Resources\LocationTypeResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use Illuminate\Http\Request;
use App\Support\PostGIS;
use App\Http\Controllers\Controller;
use App\Services\LocationService;
use App\Http\Resources\LocationTypeGeoJSONResource;

class LocationTypesController extends Controller
{

    use HandlesAPIRequestOptions;


    public function index(){

        $location_types = LocationService::queryAllLocationTypes();

        return response()->json([
            'data' => LocationTypeResource::collection($location_types)
        ]);
    }

    public function show(LocationType $location_type){

        return response()->json([
            'data' => new LocationTypeResource($location_type)
        ]);
    }
}
