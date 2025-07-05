<?php
namespace App\Services;

use App\Models\AssetCategory;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Location;
use App\Support\PostGIS;

class AssetService {

    public static function extractCategoryIds(array $filters): array | null
    {
        $categoryIds = [];
    
        if (!isset($filters['category_id'])) {
            return null;
        }
    
        $categoryFilter = $filters['category_id'];
    
        if (isset($categoryFilter['eq'])) {
            $categoryIds[] = $categoryFilter['eq'];
        }
    
        if (isset($categoryFilter['in']) && is_array($categoryFilter['in'])) {
            $categoryIds = array_merge($categoryIds, $categoryFilter['in']);
        }
    
        return array_unique($categoryIds);
    }

    public static function queryAssetCategories():Collection{

        return AssetCategory::select('id','name')
                ->whereNull('parent_id')
                ->with('children:id,name,parent_id')
                ->get();
    }

    public static function queryAssets(array $filters, bool $wants_geojson):Collection{
        
        return Asset::select('id', 'description')->assetsByCategoryID($filters, $wants_geojson)->get();

    }

    public static function queryAssetsByLocationType(int $location_type_id, array $filters, bool $wants_geojson){


        $ids = self::extractCategoryIds($filters);

        return Location::withAssets($ids)
            ->select('locations.locations.name', 'locations.locations.id')
            ->selectRaw('count(*)')
            ->where('location_type_id', $location_type_id)
            ->when($wants_geojson, function($query){
                $query->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries', 'geometry'))
                    ->groupBy('locations.geometries.geometry');
            })
            ->groupby('locations.locations.name', 'locations.locations.id')
            ->get();
    }


    
}