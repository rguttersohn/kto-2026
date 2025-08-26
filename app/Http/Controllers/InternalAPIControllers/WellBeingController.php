<?php

namespace App\Http\Controllers\InternalAPIControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Services\WellBeingService;
use App\Support\StandardizeResponse;
use Dotenv\Exception\ValidationException;
use App\Http\Resources\WellBeingScoreAsGeoJSONResource;
use App\Http\Resources\WellBeingScoreResource;


class WellBeingController extends Controller
{
    use HandlesAPIRequestOptions;

    public function getAvailableYears(int $domain_id, int $location_type_id) {

        if(!is_numeric($domain_id)){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'domain id not numeric',
                status_code: 400
            );
        }

        if(!is_numeric($location_type_id)){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'location type id not numeric',
                status_code: 400
            );
        }

        return WellBeingService::queryAvailableYears();
    }

    public function getScores(Request $request){


        $filters = $this->filters($request);


        if($filters instanceof ValidationException){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: $filters->getMessage(),
                status_code: 400
            ); 
        }

        $wants_geojson = $this->wantsGeoJSON($request);
        
        $scores = WellBeingService::queryDomainScores($filters, $wants_geojson);

        if($wants_geojson){

            return StandardizeResponse::internalAPIResponse(
                data: [
                    'type' => 'FeatureCollection',
                    'features' => WellBeingScoreAsGeoJSONResource::collection($scores)
                ]
            );

        }

        return StandardizeResponse::internalAPIResponse(
            data: WellBeingScoreResource::collection($scores)
        );

    }
}
