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
use App\Http\Controllers\PageControllers\IndexController;
use App\Http\Controllers\PageControllers\IndicatorMapController;
use App\Http\Controllers\PageControllers\IndicatorQueryController;
use App\Http\Controllers\PageControllers\IndicatorIndexController;
use App\Http\Controllers\PageControllers\IndicatorAllController;
use App\Http\Controllers\PageControllers\CommunityAllController;
use App\Http\Controllers\PageControllers\CommunityIndexController;

Route::get('/', [IndexController::class, 'index']);

Route::group([
    
    'prefix' => 'indicators'

], function(){

    Route::get('/', [IndicatorAllController::class, 'index']);

    Route::get('/{indicator_id}', [IndicatorIndexController::class, 'index']);

    Route::get('/{indicator_id}/map', [IndicatorMapController::class, 'index']);

    Route::get('/{indicator_id}/query', [IndicatorQueryController::class, 'index']);

});

Route::group([

    'prefix' => 'community-profiles'

], function(){
    
    Route::get('/', [CommunityAllController::class, 'index']);

    Route::get('/{location_id}', [CommunityIndexController::class, 'index']);
});


Route::group([
    'prefix' => 'api/app/'
], function(){

    Route::get('categories', [CategoriesController::class, 'getCategories']);

    Route::get('categories/{category_slug}', [CategoriesController::class, 'getCategory']);

    Route::get('subcategories', [CategoriesController::class, 'getSubCategories']);

    Route::get('subcategories/{subcategories_slug}', [CategoriesController::class, 'getSubCategory']);

    Route::get('breakdowns', [BreakdownsController::class, 'getBreakdowns']);

    Route::get('breakdowns/{breakdown_slug}', [BreakdownsController::class, 'getBreakdown']);

    Route::get('indicators',[IndicatorsController::class, 'getIndicators']);

    Route::get('indicators/{indicator_id}',[IndicatorsController::class, 'getIndicator']);
    
    Route::get('indicators/{indicator_id}/filters', [IndicatorsController::class, 'getIndicatorFilters']);

    Route::get('indicators/{indicator_id}/data', [IndicatorsController::class, 'getIndicatorData']);

    Route::get('location-types',[LocationTypesController::class, 'getLocationTypes']);

    Route::get('location-types/{location_type_id}',[LocationTypesController::class, 'getLocationType']);

    Route::get('locations', [LocationsController::class, 'getLocations']);

    Route::get('locations/{location_id}',[LocationsController::class, 'getLocation']);

    Route::get('locations/{location_id}/indicators',[LocationsController::class, 'getLocationIndicators']);

    Route::get('locations/{location_id}/indicators/{indicator_id}', [LocationsController::class, 'getLocationIndicator']);

    Route::get('locations/{location_id}/indicators/{indicator_id}/data', [LocationsController::class, 'getLocationIndicatorData']);

    Route::get('locations/{location_id}/indicators/{indicator_id}/filters', [LocationsController::class, 'getLocationIndicatorFilters']);

    Route::get('asset-categories',[AssetsController::class, 'getAssetCategories']);

    Route::get('asset-categories/{asset_category_id}', [AssetsController::class, 'getAssetsByCategory']);
    
    Route::get('asset-categories/{asset_category_id}/assets', [AssetsController::class, 'getAssetsOnlyByCategory']);

    Route::get('asset-categories/{asset_category_id}/custom-location', [AssetsController::class, 'getAssetsByCustomLocation']);

    Route::get('asset-categories/{asset_category_id}/location-types', [AssetsController::class, 'getAssetLocationTypes']);

    Route::get('asset-categories/{asset_category_id}/location-types/{location_type_id}', [AssetsController::class, 'getAssetsByLocationType']);

    Route::get('asset-categories/{asset_category_id}/location-types/{location_type_id}/{location_id}', [AssetsController::class, 'getAssetsByLocation']);

    Route::get('collections/', [DataCollectionsController::class, 'getCollections']);

    Route::get('collections/{collection_slug}', [DataCollectionsController::class, 'getCollection']);
    
    Route::get('collections/{collection_slug}', [DataCollectionsController::class, 'getCollection']);

    Route::get('collections/{collection_slug}/data', [DataCollectionsController::class, 'getCollectionData']);
    
    Route::get('collections/{collection_slug}/filters', [DataCollectionsController::class, 'getCollectionFilters']);

    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);


});