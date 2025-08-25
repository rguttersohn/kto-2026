<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Services\WellBeingService;
use App\Http\Resources\DomainsResource;
use App\Http\Resources\LocationTypeResource;

class WellBeingMapController extends Controller
{
    
    public function index(Request $request){

        $domains = WellBeingService::queryDomains();

        $location_types = WellBeingService::queryRankableLocationTypes();

        return Inertia::render('WellBeingMap', [
            'domains' => DomainsResource::collection($domains),
            'location_types' => LocationTypeResource::collection($location_types)
        ]);
    }

}
