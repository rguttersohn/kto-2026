<?php

namespace App\Http\Controllers\PageControllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Services\IndicatorService;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorResource;
use Illuminate\Http\Request;
use App\Services\IndicatorFiltersFormatter;
use App\Support\StandardizeResponse;

class IndicatorMapController extends Controller
{
    use HandlesAPIRequestOptions;

    public function index(Request $request, $indicator_id){
        
        $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

        $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

        $offset = $this->offset($request);

        $limit = $this->limit($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $request_filters = $this->filters($request);

        $filters = IndicatorFiltersFormatter::mergeWithDefaultFilters($indicator_filters, $request_filters);

        $sorts = $this->sorts($request);

        $indicator = IndicatorService::queryIndicatorWithData(
            $indicator_id,
            $limit,
            $offset,
            $wants_geojson,
            $filters,
            $sorts
        );

        return Inertia::render('MapIndicator', [
            'indicator' => new IndicatorResource($indicator)
        ]
        );
    }
}
