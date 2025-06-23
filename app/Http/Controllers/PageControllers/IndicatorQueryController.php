<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\IndicatorService;
use Inertia\Inertia;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\IndicatorResource;

class IndicatorQueryController extends Controller
{
    use HandlesAPIRequestOptions;

    public function index(Request $request, $indicator_id){

        $offset = $this->offset($request);

        $wants_geojson = $this->wantsGeoJSON($request);

        $filters = $this->filters($request);

        $sorts = $this->sorts($request);

        $indicator = IndicatorService::queryIndicatorWithData(
            $indicator_id,
            100,
            $offset,
            $wants_geojson,
            $filters,
            $sorts
        );
        
        return Inertia::render('QueryIndicator',[
            'indicator' => new IndicatorResource($indicator)
        ]);
    }
}
