<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoriesController extends Controller
{
    public function getCategories(){
        
        return Category::select('id', 'name', 'slug')
                ->with(['subCategories' => function($query){
                    return $query
                        ->select('id', 'parent_id', 'name', 'slug');
            }])
            ->whereNull('parent_id')
            ->get();
    }

    public function getSubCategories(){

        return Category::select('id', 'name', 'slug', 'parent_id')
        ->with(['indicators' => function($query){
            return $query->select('id', 'category_id', 'name', 'slug');
        }])
        ->whereNotNull('parent_id')
        ->get();
    }

    public function getCategory($category_slug){
        return Category::select('id', 'name', 'slug')
            ->with(['subCategories' => function($query){
                return $query
                    ->select('id', 'parent_id', 'name', 'slug');
        }])
        ->where('slug', $category_slug)
        ->get();
    }

    public function getSubCategory($subcategory_slug){
        return Category::select('id', 'name', 'slug', 'parent_id')
        ->with(['indicators' => function($query){
            return $query->select('id', 'category_id', 'name', 'slug');
        }])
        ->whereNotNull('parent_id')
        ->where('slug', $subcategory_slug)
        ->get();
    }
}
