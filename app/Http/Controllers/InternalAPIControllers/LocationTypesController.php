<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Models\LocationType;
use App\Http\Resources\LocationTypeResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Controllers\Controller;
use App\Services\LocationService;
use Illuminate\Http\Request;


class LocationTypesController extends Controller
{

    use HandlesAPIRequestOptions;


    public function index(Request $request){
        
        $filters = $this->filters($request);

        $location_types = LocationService::queryAllLocationTypes(filters: $filters);

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
