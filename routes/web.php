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
use App\Http\Controllers\PageControllers\WellBeingMapController;
use App\Http\Controllers\InternalAPIControllers\WellBeingController;
use App\Models\WellBeingScore;

Route::get('/', [IndexController::class, 'index']);

Route::group([
    
    'prefix' => 'indicators'

], function(){

    Route::get('/', [IndicatorAllController::class, 'index']);

    Route::get('/{indicator_id}', [IndicatorIndexController::class, 'index']);

    Route::get('/{indicator_id}/map', [IndicatorMapController::class, 'index']);

    Route::get('/{indicator_id}/query', [IndicatorQueryController::class, 'index']);

});

Route::get('/community-assets', [AssetsMapController::class, 'index']);


Route::group([

    'prefix' => 'community-profiles'

], function(){
    
    Route::get('/', [CommunityAllController::class, 'index']);

    Route::get('/{location_id}', [CommunityIndexController::class, 'index']);
});

Route::group([
    'prefix' => 'well-being'
], function(){

    Route::get('/', [WellBeingMapController::class, 'index']);

});



/**
 * Internal API
 */

Route::group([
    'prefix' => 'api/app/'
], function(){
    
    /**
     *  Indicator end points
     */

    Route::get('indicators/{indicator_id}/data', [IndicatorsController::class, 'getIndicatorData']);

    Route::get('indicators/{indicator_id}/data/count', [IndicatorsController::class, 'getIndicatorDataCount']);

    Route::get('indicators/{indicator_id}/data/export', [IndicatorsController::class, 'getIndicatorExport']);

    /**
     * Community Asset Endpoints
     */

    Route::get('assets', [AssetsController::class, 'getAssets']);

    Route::get('assets/aggregate-location-type', [AssetsController::class, 'getAggregatedAssetsByLocationType']);
    
    Route::get('assets/aggregate-location', [AssetsController::class, 'getAggregatedAssetsByLocation']);

    Route::post('assets/aggregate-custom-location', [AssetsController::class, 'getAggregatedAssetsByCustomLocation']);

    /**
     * Community Profile Endpoints
     */

    Route::get('location-types/{location_type_id}',[LocationTypesController::class, 'getLocationType']);

    Route::get('locations/{location_id}/indicators/{indicator_id}/data', [LocationsController::class, 'getLocationIndicatorData']);
    
    Route::get('locations/{location_id}/indicators/{indicator_id}/filters', [LocationsController::class, 'getLocationIndicatorFilters']);

    Route::get('locations/{location_id}/well-being', [LocationsController::class, 'getLocationDomainScore']);
   
    /**
     * 
     * Well Being Endpoints
     */


    Route::get('well-being', [WellBeingController::class, 'getScores']);

    /**
     * 
     * Search
     */
    
    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);


});