<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use inertia\Inertia;
use App\Services\IndicatorService;

class IndicatorIndexController extends Controller
{
    public function index($indicator_id){

        return Inertia::render('IndicatorIndex', [
            'indicator' => IndicatorService::queryIndicator($indicator_id)
        ]);
    }
}
