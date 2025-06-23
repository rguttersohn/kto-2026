<?php

namespace App\Http\Controllers\PageControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Services\LocationService;
use App\Http\Resources\LocationTypeResource;

class CommunityAllController extends Controller
{
    public function index(){

        $location_types = LocationService::queryAllLocationTypes();

        return Inertia::render('CommunityAll', [
            'location_types' => LocationTypeResource::collection($location_types)
        ]);
        
    }
}
