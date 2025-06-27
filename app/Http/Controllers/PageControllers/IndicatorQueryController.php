<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\IndicatorService;
use Inertia\Inertia;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorDataResource;
use App\Http\Resources\IndicatorResource;
use App\Services\IndicatorFiltersFormatter;
use App\Http\Resources\IndicatorFiltersResource;
use App\Http\Resources\IndicatorInitialFiltersResource;

class IndicatorQueryController extends Controller
{
    use HandlesAPIRequestOptions;

    public function index(Request $request, $indicator_id){

        $indicator_filters_unformatted = IndicatorService::queryIndicatorFilters($indicator_id);

        $indicator_filters = IndicatorFiltersFormatter::formatFilters($indicator_filters_unformatted)['data'];

        $offset = $this->offset($request);

        $request_filters = $this->filters($request);
        
        $sorts = $this->sorts($request);

        $indicator = IndicatorService::queryIndicator($indicator_id);
        
        $data = IndicatorService::queryData(
            $indicator_id,
            50,
            $offset,
            false,
            $request_filters,
            $sorts
        );

        $data_count = IndicatorService::queryDataCount($indicator_id, $request_filters);

        return Inertia::render('IndicatorQuery',[
            'indicator' => new IndicatorResource($indicator),
            'data' => IndicatorDataResource::collection($data),
            'data_count' => $data_count,
            'filters' =>  new IndicatorFiltersResource($indicator_filters),
            'initial_filters' => new IndicatorInitialFiltersResource($request_filters)
        ]);
    }
}
