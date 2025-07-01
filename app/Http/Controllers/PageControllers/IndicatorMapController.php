<?php

namespace App\Http\Controllers\PageControllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Services\IndicatorService;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorFiltersResource;
use App\Http\Resources\IndicatorGeoJSONDataResource;
use App\Http\Resources\IndicatorInitialFiltersResource;
use App\Http\Resources\IndicatorResource;
use Illuminate\Http\Request;
use App\Services\IndicatorFiltersFormatter;
use App\Support\GeoJSON;
 

class IndicatorMapController extends Controller
{
    use HandlesAPIRequestOptions;

    public function index(Request $request, $indicator_id){
        
        $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

        $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

        $offset = $this->offset($request);

        $limit = $this->limit($request);

        $request_filters = $this->filters($request);

        $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator_filters, $request_filters);
        
        $sorts = $this->sorts($request);

        $indicator = IndicatorService::queryIndicator($indicator_id);
        
        $data = IndicatorService::queryData(
            $indicator_id,
            $limit,
            $offset,
            true,
            $filters,
            $sorts
        );

        $init_filters = IndicatorFiltersFormatter::toSelectedFilters($filters, $indicator_filters);
       
        return Inertia::render('IndicatorMap', [
            'indicator' => new IndicatorResource($indicator),
            'data' => GeoJSON::wrapGeoJSONResource(IndicatorGeoJSONDataResource::collection($data)),
            'filters' =>  new IndicatorFiltersResource($indicator_filters),
            'initial_selected_filters' => new IndicatorInitialFiltersResource($init_filters)
        ]);
    }
}
