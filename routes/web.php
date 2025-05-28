<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BreakdownsController;
use App\Http\Controllers\IndicatorsController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\LocationTypesController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\DataCollectionsController;


Route::get('/', function () {
    return view('welcome');
});


Route::group([
    'prefix' => 'api'
], function(){

    Route::get('categories', [CategoriesController::class, 'getCategories']);

    Route::get('categories/{category_slug}', [CategoriesController::class, 'getCategory']);

    Route::get('subcategories', [CategoriesController::class, 'getSubCategories']);

    Route::get('subcategories/{subcategories_slug}', [CategoriesController::class, 'getSubCategory']);

    Route::get('breakdowns', [BreakdownsController::class, 'getBreakdowns']);

    Route::get('breakdowns/{breakdown_slug}', [BreakdownsController::class, 'getBreakdown']);

    Route::get('indicators',[IndicatorsController::class, 'getIndicators']);

    Route::get('indicators/{indicator_slug}',[IndicatorsController::class, 'getIndicator']);
    
    Route::get('indicators/{indicator_slug}/filters', [IndicatorsController::class, 'getIndicatorFilters']);

    Route::get('indicators/{indicator_slug}/data', [IndicatorsController::class, 'getIndicatorData']);

    Route::get('location-types',[LocationTypesController::class, 'getLocationTypes']);

    Route::get('location-types/{location_type_slug}',[LocationTypesController::class, 'getLocationType']);

    Route::get('locations', [LocationsController::class, 'getLocations']);

    Route::get('locations/{location_id}',[LocationsController::class, 'getLocation']);

    Route::get('locations/{location_id}/indicators',[LocationsController::class, 'getLocationIndicators']);

    Route::get('locations/{location_id}/indicators/{indicator_slug}', [LocationsController::class, 'getLocationIndicator']);

    Route::get('locations/{location_id}/indicators/{indicator_slug}/data', [LocationsController::class, 'getLocationIndicatorData']);

    Route::get('locations/{location_id}/indicators/{indicator_slug}/filters', [LocationsController::class, 'getLocationIndicatorFilters']);

    Route::get('asset-categories',[AssetsController::class, 'getAssetCategories']);

    Route::get('asset-categories/{asset_category_slug}', [AssetsController::class, 'getAssetsByCategory']);

    Route::get('asset-categories/{asset_category_slug}/custom-location', [AssetsController::class, 'getAssetsByCustomLocation']);

    Route::get('asset-categories/{asset_category_slug}/{location_type_slug}', [AssetsController::class, 'getAssetsByLocationType']);

    Route::get('asset-categories/{asset_category_slug}/{location_type_slug}/{location_id}', [AssetsController::class, 'getAssetsByLocation']);

    Route::get('collections/', [DataCollectionsController::class, 'getCollections']);

    Route::get('collections/{collection_slug}', [DataCollectionsController::class, 'getCollection']);
    
    Route::get('collections/{collection_slug}', [DataCollectionsController::class, 'getCollection']);

    Route::get('collections/{collection_slug}/data', [DataCollectionsController::class, 'getCollectionData']);
    
    Route::get('collections/{collection_slug}/filters', [DataCollectionsController::class, 'getCollectionFilters']);

    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);


});