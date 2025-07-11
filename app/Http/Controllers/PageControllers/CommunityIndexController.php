<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Services\LocationService;
use Inertia\Inertia;
use App\Services\IndicatorService;
use App\Http\Resources\IndicatorsResource;
use App\Services\AssetService;

class CommunityIndexController extends Controller
{
    
    public function index($location_id){

        $location = LocationService::queryLocation($location_id);

        $indicators = IndicatorService::queryAllIndicators();

        if(!$location){

            return abort(404);
        }
        
        return Inertia::render('CommunityIndex',[
            'location' => new LocationResource($location),
            'indicators' => IndicatorsResource::collection($indicators),
            'asset_categories' => AssetService::queryAssetCategories()
        ]);

    }
}
