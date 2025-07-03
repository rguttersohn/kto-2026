<?php

namespace App\Http\Controllers\InternalAPIControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetsGeoJSONResource;
use App\Services\AssetService;
use App\Support\StandardizeResponse;
use App\Http\Resources\AssetsResource;
use App\Support\GeoJSON;

class AssetsController extends Controller
{

    use HandlesAPIRequestOptions;


    public function getAssets(Request $request){

        $filters = $this->filters($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        if(!$filters){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'one filter required',
                status_code: 404
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

}
