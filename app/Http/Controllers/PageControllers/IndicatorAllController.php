<?php

namespace App\Http\Controllers\PageControllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Services\IndicatorService;
use App\Http\Resources\IndicatorsResource;

class IndicatorAllController extends Controller
{
    public function index(){

        $indicators = IndicatorService::queryAllIndicators();

        return Inertia::render('IndicatorAll', [
            'indicators' => IndicatorsResource::collection($indicators)
        ]);
        
    }
}
