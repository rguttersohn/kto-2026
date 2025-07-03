<?php
namespace App\Services;

use App\Models\AssetCategory;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;

class AssetService {

    public static function queryAssetCategories():Collection{

        return AssetCategory::select('id','name')
                ->whereNull('parent_id')
                ->with('children:id,name,parent_id')
                ->get();
    }

    public static function queryAssets(array $filters, bool $wants_geojson):Collection{
        
        return Asset::select('id', 'description')->assetsByCategoryID($filters, $wants_geojson)->get();

    }


    
}