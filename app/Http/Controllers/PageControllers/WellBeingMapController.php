<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Services\WellBeingService;
use App\Http\Resources\DomainsResource;
use App\Http\Resources\LocationTypeResource;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\WellBeingScoreAsGeoJSONResource;
use App\Http\Resources\WellBeingScoreResource;

class WellBeingMapController extends Controller
{   
    use HandlesAPIRequestOptions;
    
    public function index(Request $request){

        $filters = $this->filters($request);

        $wants_geojson = $this->wantsGeoJSON($request);
        
        $domains = WellBeingService::queryDomains();

        $location_types = WellBeingService::queryRankableLocationTypes();

        $scores = WellBeingService::queryDomainScores($filters, $wants_geojson);

        if($wants_geojson){

            $scores_resource = [
                'type' => 'FeatureCollection',
                'features' => WellBeingScoreAsGeoJSONResource::collection($scores)
            ];

        } else {

            $scores_resource = WellBeingScoreResource::collection($scores);
        }
    
        return Inertia::render('WellBeingMap', [
            'domains' => DomainsResource::collection($domains),
            'location_types' => LocationTypeResource::collection($location_types),
            'scores' => $scores_resource,
            'indicators' => WellBeingService::queryDomainIndicators($filters)
        ]);
    }

}
