<?php

namespace App\Http\Controllers\PageControllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Services\IndicatorService;

class IndicatorAllController extends Controller
{
    public function index(){

        return Inertia::render('IndicatorAll', [
            'indicators' => IndicatorService::queryAllIndicators()
        ]);
        
    }
}
