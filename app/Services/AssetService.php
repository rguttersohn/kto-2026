<?php
namespace App\Services;

use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Collection;

class AssetService {

    public static function queryAssetCategories():Collection{

        return AssetCategory::select('id','name')
                ->whereNull('parent_id')
                ->with('children:id,name,parent_id')
                ->get();
    }


    
}