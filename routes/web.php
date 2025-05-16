<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BreakdownsController;
use App\Http\Controllers\IndicatorsController;
use App\Http\Controllers\LocationsController;


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

    Route::get('location-types',[LocationsController::class, 'getLocationTypes']);

    Route::get('location-types/{location_type_slug}',[LocationsController::class, 'getLocationType']);

    Route::get('location-types/{location_type_slug}/{location_id}',[LocationsController::class, 'getLocation']);

    Route::get('location-types/{location_type_slug}/{location_id}/indicators',[LocationsController::class, 'getLocationIndicators']);

    Route::get('location-types/{location_type_slug}/{location_id}/indicators/{indicator_slug}', [LocationsController::class, 'getLocationIndicator']);

    Route::get('location-types/{location_type_slug}/{location_id}/indicators/{indicator_slug}/data', [LocationsController::class, 'getLocationIndicatorData']);


    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);


});