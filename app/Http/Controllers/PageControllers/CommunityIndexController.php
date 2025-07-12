<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetCategoriesResource;
use App\Http\Resources\LocationResource;
use App\Services\LocationService;
use Inertia\Inertia;
use App\Services\IndicatorService;
use App\Http\Resources\IndicatorsResource;
use App\Services\AssetService;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorFiltersResource;
use App\Http\Resources\IndicatorResource;
use App\Services\IndicatorFiltersFormatter;

class CommunityIndexController extends Controller
{
    use HandlesAPIRequestOptions;
    
    public function index(Request $request, $location_id){

        $location = LocationService::queryLocation($location_id);

        if(!$location){

            return abort(404);
        }

        $indicators = IndicatorService::queryAllIndicators();

        $asset_categories = AssetService::queryAssetCategories();
        
        $requested_indicator = $this->indicator($request);

        if(!$requested_indicator){

            $current_indicator = null;
            $current_indicator_filters = null;

        } else {

            $current_indicator = IndicatorService::queryIndicator($requested_indicator);
            $current_indicator_filters = IndicatorService::queryIndicatorFilters($requested_indicator);

            $indicator_filters_formatted = IndicatorFiltersFormatter::formatFilters($current_indicator_filters);

        }
        
        return Inertia::render('CommunityIndex',[
            'location' => new LocationResource($location),
            'indicators' => IndicatorsResource::collection($indicators),
            'current_indicator' => $current_indicator ? new IndicatorResource($current_indicator) : null,
            'current_indicator_filters' => $current_indicator_filters ? new IndicatorFiltersResource($indicator_filters_formatted['data']) : null,
            'asset_categories' => AssetCategoriesResource::collection($asset_categories)
        ]);

    }
}
