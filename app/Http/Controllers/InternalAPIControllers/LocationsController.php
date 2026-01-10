<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Models\Location;
use App\Support\StandardizeResponse;
use Illuminate\Http\Request;
use App\Models\Indicator;
use App\Services\IndicatorFiltersFormatter;
use Illuminate\Validation\ValidationException;
use App\Services\IndicatorService;
use App\Http\Controllers\Controller;
use App\Http\Resources\IndicatorFiltersResource;
use App\Http\Resources\LocationGeoJSONResource;
use App\Http\Resources\LocationResource;
use App\Services\LocationService;
use App\Services\WellBeingService;
use App\Models\LocationType;
use App\Http\Resources\LocationTypeResource;
use App\Support\GeoJSON;


class LocationsController extends Controller
{
    use HandlesAPIRequestOptions;
    
    public function index(LocationType $location_type){

        $wants_geojson = $this->wantsGeoJSON(request());

        $locations = LocationService::queryLocationTypeWithLocation($location_type->id, $wants_geojson);

        return response()->json([
            'data' => new LocationTypeResource($locations)
        ]);

    }

    public function show(Request $request, LocationType $location_type, Location $location){

        $wants_geojson = $this->wantsGeoJSON($request);

        $location = LocationService::queryLocation($location_type->id, $location->id, $wants_geojson);


        if($wants_geojson){
 
            return response()->json([
                'data' => Geojson::wrapGeoJSONResource(new LocationGeoJSONResource($location))
            ]);
        }

        return response()->json([
            'data' => new LocationResource($location)
        ]);

    }

    public function indicatorIndex(LocationType $location_type, Location $location){
                
        $location = LocationService::queryLocationIndicators($location_type->id, $location->id);
        
        return response()->json([
            'data' => new LocationResource($location)
        ]);

    }


}
