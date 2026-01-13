<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalAPIControllers\SearchController;
use App\Http\Controllers\InternalAPIControllers\IndicatorsController;
use App\Http\Controllers\InternalAPIControllers\LocationsController;
use App\Http\Controllers\InternalAPIControllers\LocationTypesController;
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

/**
 * 
 * API Endpoints
 * 
 */

Route::domain('api.' . config('app.url'))->group(function(){

/**
 * Internal API
 */

    Route::group([
        
        'prefix' => 'app/v1'

    ], function(){
        
        /**
         *  Indicator end points
         */

        Route::get('indicators', [IndicatorsController::class, 'index'])->name('api.app.indicators.index');

        Route::get('indicators/search', [IndicatorsController::class, 'search'])->name('api.app.indicators.search');

        Route::get('indicators/{indicator}', [IndicatorsController::class, 'show'])->name('api.app.indicators.show');

        Route::get('indicators/{indicator}/data', [IndicatorsController::class, 'data'])->name('api.app.indicators.data');

        Route::get('indicators/{indicator}/data/count', [IndicatorsController::class, 'count'])->name('api.app.indicators.data.count');

        Route::get('indicators/{indicator}/data/export', [IndicatorsController::class, 'export'])->name('api.app.indicators.data.export');

        Route::get('indicators/{indicator}/filters', [IndicatorsController::class, 'availableFilters'])->name('api.app.indicators.filters');

        /**
         * Community Profile Endpoints
         */

        Route::get('location-types', [LocationTypesController::class, 'index'])->name('api.app.location_types.index');
        
        Route::get('location-types/{location_type}',[LocationTypesController::class, 'show'])->name('api.app.location_types.show');

        Route::get('location-types/{location_type}/indicators',[LocationTypesController::class, 'indicatorIndex'])->name('api.app.location_types.indicators.index');
        
        Route::get('location-types/{location_type}/indicators/search', [LocationTypesController::class, 'indicatorSearch'])->name('api.app.location_types.indicators.search');

        Route::get('location-types/{location_type}/locations', [LocationsController::class, 'index'])->name('api.app.location_types.locations.index');

        Route::get('location-types/{location_type}/locations/{location}', [LocationsController::class, 'show'])->name('api.app.location_types.locations.show');

        
    });

});