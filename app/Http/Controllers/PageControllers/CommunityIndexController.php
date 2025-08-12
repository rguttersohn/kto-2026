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
use App\Http\Resources\IndicatorDataResource;
use App\Http\Resources\IndicatorFiltersResource;
use App\Http\Resources\IndicatorResource;
use App\Http\Resources\LocationGeoJSONResource;
use App\Services\IndicatorFiltersFormatter;
use App\Support\GeoJSON;
use Dotenv\Exception\ValidationException;
use App\Services\WellBeingService;
use App\Http\Resources\DomainsResource;

class CommunityIndexController extends Controller
{
    use HandlesAPIRequestOptions;
    
    public function index(Request $request, $location_id){

        $location = LocationService::queryLocation($location_id, true);
        
        if(!$location){

            return abort(404);
        }

        $indicators = IndicatorService::queryAllIndicators();

        $asset_categories = AssetService::queryAssetCategories();
        
        $requested_indicator = $this->indicator($request);

        if(!$requested_indicator){

            $current_indicator = null;
            $current_indicator_filters = null;
            $current_indicator_data = null;

        } else {

            $current_indicator = IndicatorService::queryIndicator($requested_indicator);
            $current_indicator_filters = IndicatorService::queryIndicatorFilters($requested_indicator);

            $indicator_filters_formatted = IndicatorFiltersFormatter::formatFilters($current_indicator_filters);

            $filters = $this->filters($request);

            if(!$filters || $filters instanceof ValidationException){

                $current_indicator_data = null;

            } else {

                $current_indicator_data = IndicatorService::queryData(
                    indicator_id: $requested_indicator,
                    limit: 3000,
                    offset: 0,
                    wants_geojson:false, 
                    filters:$filters,
                    sorts:[],
                    location_id:$location_id
                );
            }

        }

        $location_has_ranking = LocationService::queryIsLocationTypeRanked($location->location_type_id);
        
        if($location_has_ranking){
        
            $well_being_domains = DomainsResource::collection(WellBeingService::queryDomains());
        
        } else {

            $well_being_domains = null;
        
        }
    
        return Inertia::render('CommunityIndex',[
            'location' => new LocationResource($location),
            'location_geojson' => GeoJSON::wrapGeoJSONResource(new LocationGeoJSONResource($location)),
            'indicators' => IndicatorsResource::collection($indicators),
            'current_indicator' => $current_indicator ? new IndicatorResource($current_indicator) : null,
            'current_indicator_filters' => $current_indicator_filters ? new IndicatorFiltersResource($indicator_filters_formatted['data']) : null,
            'current_indicator_data' => $current_indicator_data  ? IndicatorDataResource::collection($current_indicator_data) : null,
            'asset_categories' => AssetCategoriesResource::collection($asset_categories),
            'location_has_ranking' => $location_has_ranking,
            'well_being_domains' => $well_being_domains
        ]);

    }
}
