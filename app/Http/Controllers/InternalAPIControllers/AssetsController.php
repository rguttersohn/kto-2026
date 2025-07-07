<?php

namespace App\Http\Controllers\InternalAPIControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetCountResource;
use App\Http\Resources\AssetsByLocationTypeResource;
use App\Http\Resources\AssetsGeoJSONResource;
use App\Services\AssetService;
use App\Support\StandardizeResponse;
use App\Http\Resources\AssetsResource;
use App\Support\GeoJSON;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\AssetsAsGeoJSONByLocationTypeResource;

class AssetsController extends Controller
{

    use HandlesAPIRequestOptions;


    public function getAssets(Request $request){

        $filters = $this->filters($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        if(!$filters || $filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'one filter required',
                status_code: 400
            );
        }

        $assets = AssetService::queryAssets($filters, $wants_geojson);

        if($wants_geojson){

            return StandardizeResponse::internalAPIResponse(
                data: GeoJSON::wrapGeoJSONResource(AssetsGeoJSONResource::collection($assets))
            );
        }

        return StandardizeResponse::internalAPIResponse(
            data: AssetsResource::collection($assets)
        );

    }

    public function getAggregatedAssets(Request $request){


        $location_type = $this->locationType($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $filters = $this->filters($request);

        if(!$filters || $filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'one filter required',
                status_code: 400
            );
        }

        $assets = AssetService::queryAssetsByLocationType($location_type, $filters, $wants_geojson);


        if($wants_geojson){

            return StandardizeResponse::internalAPIResponse(
                data: GeoJSON::wrapGeoJSONResource(AssetsAsGeoJSONByLocationTypeResource::collection($assets))
            );

        }

        return StandardizeResponse::internalAPIResponse(
            data: AssetsByLocationTypeResource::collection($assets)
        );

    }

    public function getAggregatedAssetsByCustomLocation(Request $request){

        $filters = $this->filters($request);

        $geometry = $this->geometry($request);

        if(!$geometry){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'missing geometry',
                status_code: 400
            );
        }

        if($geometry instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $geometry->getMessage(),
                status_code: 400
            );
        }


        if(!$filters){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'requires one filter',
                status_code: 400
            );
        }

        if($filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $filters->getMessage(),
                status_code: 400
            );
        }

        $assets = AssetService::queryAssetsByCustomLocaton($geometry, $filters);
        
        return StandardizeResponse::internalAPIResponse(
            data: [
                'geometry' => $geometry,
                'assets' => new AssetCountResource($assets)
            ]
        );
    }

}
