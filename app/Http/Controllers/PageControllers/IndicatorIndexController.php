<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use inertia\Inertia;
use App\Services\IndicatorService;
use App\Http\Resources\IndicatorResource;

class IndicatorIndexController extends Controller
{
    public function index($indicator_id){

        if(!is_numeric($indicator_id)){

            return abort(404);
        }

        $indicator = IndicatorService::queryIndicator($indicator_id);

        if(!$indicator){

            return abort(404);
        }

        return Inertia::render('IndicatorIndex', [
            'indicator' => new IndicatorResource($indicator)
        ]);
    }
}
