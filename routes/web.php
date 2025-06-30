<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalAPIControllers\SearchController;
use App\Http\Controllers\InternalAPIControllers\IndicatorsController;
use App\Http\Controllers\InternalAPIControllers\LocationsController;
use App\Http\Controllers\InternalAPIControllers\LocationTypesController;
use App\Http\Controllers\InternalAPIControllers\AssetsController;
use App\Http\Controllers\PageControllers\IndexController;
use App\Http\Controllers\PageControllers\IndicatorMapController;
use App\Http\Controllers\PageControllers\IndicatorQueryController;
use App\Http\Controllers\PageControllers\IndicatorIndexController;
use App\Http\Controllers\PageControllers\IndicatorAllController;
use App\Http\Controllers\PageControllers\CommunityAllController;
use App\Http\Controllers\PageControllers\CommunityIndexController;
use App\Http\Controllers\PageControllers\AssetsMapController;

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

Route::get('/community-assets', [AssetsMapController::class, 'index']);


Route::group([
    'prefix' => 'api/app/'
], function(){

    Route::get('indicators/{indicator_id}/data', [IndicatorsController::class, 'getIndicatorData']);

    Route::get('indicators/{indicator_id}/data/count', [IndicatorsController::class, 'getIndicatorDataCount']);

    Route::get('location-types/{location_type_id}',[LocationTypesController::class, 'getLocationType']);

    Route::get('locations/{location_id}/indicators/{indicator_id}/data', [LocationsController::class, 'getLocationIndicatorData']);

    Route::get('locations/{location_id}/indicators/{indicator_id}/filters', [LocationsController::class, 'getLocationIndicatorFilters']);

    Route::get('asset-categories',[AssetsController::class, 'getAssetCategories']);

    Route::get('asset-categories/{asset_category_id}', [AssetsController::class, 'getAssetsByCategory']);
    
    Route::get('asset-categories/{asset_category_id}/assets', [AssetsController::class, 'getAssetsOnlyByCategory']);

    Route::get('asset-categories/{asset_category_id}/custom-location', [AssetsController::class, 'getAssetsByCustomLocation']);

    Route::get('asset-categories/{asset_category_id}/location-types', [AssetsController::class, 'getAssetLocationTypes']);

    Route::get('asset-categories/{asset_category_id}/location-types/{location_type_id}', [AssetsController::class, 'getAssetsByLocationType']);

    Route::get('asset-categories/{asset_category_id}/location-types/{location_type_id}/{location_id}', [AssetsController::class, 'getAssetsByLocation']);

    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);


});