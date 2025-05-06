<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Http\Controllers\CategoriesController;
use App\Models\Indicator;
use App\Http\Controllers\SearchController;

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

    Route::get('search', [SearchController::class, 'getKeywordSearchResults']);

    Route::get('ai-search', [SearchController::class, 'getAISearchResults']);

});