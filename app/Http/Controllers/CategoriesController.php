<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoriesController extends Controller
{
    public function getCategories(){
        
        return Category::select('id', 'name')
                ->with(['subCategories' => function($query){
                    return $query
                        ->select('id', 'parent_id', 'name');
            }])
            ->whereNull('parent_id')
            ->get();
    }

    public function getSubCategories(){

        return Category::select('id', 'name', 'parent_id')
        ->with(['indicators' => function($query){
            return $query->select('id', 'category_id', 'name');
        }])
        ->whereNotNull('parent_id')
        ->get();
    }

    public function getCategory($category_id){
        return Category::select('id', 'name')
            ->with(['subCategories' => function($query){
                return $query
                    ->select('id', 'parent_id', 'name');
    }])
        ->where('id', $category_id)
        ->get();
    }

    public function getSubCategory($subcategory_id){
        return Category::select('id', 'name', 'parent_id')
        ->with(['indicators' => function($query){
            return $query->select('id', 'category_id', 'name');
        }])
        ->whereNotNull('parent_id')
        ->where('id', $subcategory_id)
        ->get();
    }
}
