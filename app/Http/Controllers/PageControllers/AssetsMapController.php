<?php

namespace App\Http\Controllers\PageControllers;

use App\Services\AssetService;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetCategoriesResource;
use App\Http\Resources\LocationTypeResource;
use App\Services\LocationService;

class AssetsMapController extends Controller
{
    public function index(){

        $asset_categories = AssetService::queryAssetCategories();
        $location_types = LocationService::queryAllLocationTypes();

        return Inertia::render('AssetsMap', [
            'asset_categories' => AssetCategoriesResource::collection($asset_categories),
            'location_types' => LocationTypeResource::collection($location_types)
        ]);
    } 
}
