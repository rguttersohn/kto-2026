<?php
namespace App\Services;

use App\Models\AssetCategory;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Location;
use App\Support\PostGIS;
use Illuminate\Support\Facades\DB;

class AssetService {

    public static function extractCategoryIds(array $filters): array | null
    {
        $categoryIds = [];
    
        if (!isset($filters['category'])) {
            return null;
        }
    
        $categoryFilter = $filters['category'];
    
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

        $locations = LocationService::queryLocationsByLocationType($location_type_id, $wants_geojson);
        
        $location_ids = $locations->pluck('id');

        $filter_ids = self::extractCategoryIds($filters);
        
        $assets = Location::withAssets($filter_ids)
            ->select('locations.locations.id as location_id', 'asset_categories.id', 'asset_categories.name')
            ->selectRaw('count(assets.assets.id)')
            ->whereIn('locations.id', $location_ids)
            ->groupby('locations.locations.id', 'asset_categories.id', 'asset_categories.name')
            ->get();

        return $locations->map(function($location)use($assets){
            
            return [
                'id' => $location->id,
                'name' => $location->name,
                'geometry' => $location->geometry ?? null,
                'count' => $assets->where('location_id', $location->id)->values()
            ];
        });

    }

    public static function queryAssetsByLocation(int $location_id, array $filters, bool $wants_geojson){

        $location = LocationService::queryLocation($location_id, $wants_geojson);

        $filter_ids = self::extractCategoryIds($filters);

        $assets = Location::withAssets($filter_ids)            
            ->select('locations.locations.id as location_id', 'ac.id', 'ac.name')
            ->selectRaw('count(assets.assets.id)')
            ->where('locations.id', $location->id)
            ->groupby('locations.locations.id', 'ac.id', 'ac.name')
            ->get();

        return [
                'id' => $location->id,
                'name' => $location->name,
                'geometry' => $location->geometry ?? null,
                'count' => $assets->where('location_id', $location->id)->values()
            ];

    }

    public static function queryAssetsByCustomLocaton(array $custom_location, array $filters){


        return Asset::select('asset_categories.name', 'asset_categories.id')
            ->selectRaw('count(*)')
            ->join('asset_categories', 'assets.asset_category_id', '=', 'asset_categories.id')
            ->isGeometryWithinGeoJSON('assets.geometry', $custom_location)
            ->filter($filters)
            ->groupBy('asset_categories.name', 'asset_categories.id')
            ->get();

    }


    
}