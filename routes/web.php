<?php

use App\Http\Controllers\InternalAPIControllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalAPIControllers\IndicatorsController;
use App\Http\Controllers\InternalAPIControllers\LocationsController;
use App\Http\Controllers\InternalAPIControllers\LocationTypesController;
use App\Http\Controllers\InternalAPIControllers\DomainController;
use Illuminate\Http\Request;

Route::get('/', fn()=>response()->json(['message' => 'welcome']));

/***
 * 
 * Sanctum
 * 
 */

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});


/**
 * Internal API
 */

Route::prefix('app/v1')->group(function(){
    
    /***
     * 
     * Current User
     * 
     */

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user()->toResource();
    });

    Route::middleware(['auth:sanctum', 'web'])->post('/logout', [UserController::class, 'logout']);


    //Should use web middleware for checking if user is an editor or admin

    Route::middleware('web')->group(function(){
        
        /***
         * 
         * Domain Endpoints
         * 
         */
        
        Route::get('domains', [DomainController::class, 'index'])->name('api.app.domain.index');

        /**
         *  Indicator end points
         */

        Route::get('indicators', [IndicatorsController::class, 'index'])->name('api.app.indicators.index');

        Route::get('indicators/{indicator}', [IndicatorsController::class, 'show'])->name('api.app.indicators.show');

        Route::get('indicators/{indicator}/data', [IndicatorsController::class, 'data'])->name('api.app.indicators.data');

        Route::get('indicators/{indicator}/data/count', [IndicatorsController::class, 'count'])->name('api.app.indicators.data.count');

        Route::get('indicators/{indicator}/data/export', [IndicatorsController::class, 'export'])->name('api.app.indicators.data.export');

        Route::get('indicators/{indicator}/filters', [IndicatorsController::class, 'availableFilters'])->name('api.app.indicators.filters');

        /**
         * Location Endpoints
         */

        Route::get('location-types', [LocationTypesController::class, 'index'])->name('api.app.location_types.index');
        
        Route::get('location-types/{location_type}',[LocationTypesController::class, 'show'])->name('api.app.location_types.show');
        
        Route::get('location-types/{location_type}/locations', [LocationsController::class, 'index'])->name('api.app.location_types.locations.index');

        Route::get('locations/{location}', [LocationsController::class, 'show'])->name('api.app.locations.show');

        Route::get('locations/{location}/indicators', [LocationsController::class, 'indicatorIndex'])->name('api.app.location.indicators.index');

        Route::get('locations/{location}/peers', [LocationsController::class, 'peersIndex'])->name('api.app.location.peers.index');
    
    });

});
