<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BreakdownsController;
use App\Http\Controllers\IndicatorsController;


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

    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);


});