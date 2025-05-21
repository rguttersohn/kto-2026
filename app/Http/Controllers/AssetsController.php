<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Support\PostGIS;
use App\Models\LocationType;
use App\Support\StandardizeResponse;
use App\Models\Asset;
use App\Models\Location;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;

class AssetsController extends Controller
{

    use HandlesAPIRequestOptions;
    
    public function getAssetCategories(){

        return AssetCategory::select('id','name', 'slug')
            ->whereNull('parent_id')
            ->get();

    }

    public function getAssetsByCategory(Request $request, $asset_category_slug){
        
        $wants_geojson = $this->wantsGeoJSON($request);

        $subcategory = $this->subcategory($request);

        $asset_category = AssetCategory::defaultSelects()
            ->when(!$subcategory, fn($query)=>$query->with(['children'=> fn($query)=>$query->defaultSelects()]))
            ->where('slug', $asset_category_slug)
            ->firstOrFail();

        if(!$asset_category){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'asset category slug not found',
                status_code: 404
            );
        }

        $child_category_ids = $subcategory ?? $asset_category->children->pluck('id')->toArray();


        if(!$child_category_ids){

            $assets = Asset::assetsByCategoryID($wants_geojson, $asset_category->id)->get();

        } else {

            $assets = Asset::assetsByCategoryID($wants_geojson, $child_category_ids)->get();

        }

        if($wants_geojson){

            return StandardizeResponse::APIResponse(
                data: [
                    'asset_category' => $asset_category,
                    'assets' => Asset::getAssetsAsGeoJSON($assets),
                ]
            );
        }

        return StandardizeResponse::APIResponse(
            
            data: [
                'asset_category' => $asset_category,
                'assets' => $assets,
            ]
        );

        
    }

    public function getAssetsByLocationType(Request $request, $asset_category_slug,$location_type_slug){

        $wants_geojson = $this->wantsGeoJSON($request);

        $subcategory = $this->subcategory($request);

        $asset_category = AssetCategory::defaultSelects()
            ->when(!$subcategory, fn($query)=>$query->with(['children' => fn($query)=>$query->defaultSelects()]))
            ->where('slug', $asset_category_slug)
            ->firstOrFail();

        if(!$asset_category){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'asset category slug not found',
                status_code: 404
            );
        }

        $child_category_ids = $subcategory ?? $asset_category->children->pluck('id')->toArray();
        
        $location_type = LocationType::defaultSelects()
            ->where('locations.location_types.slug', $location_type_slug)
            ->with(['locations' => function($query)use($asset_category, $wants_geojson, $child_category_ids){
                
                $query
                    ->select('locations.locations.id','locations.locations.location_type_id', 'locations.locations.name')
                    ->selectRaw('count(assets.assets.*) as assets')
                    ->when($wants_geojson, function($query){
                        
                        $query
                            ->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries','geometry', .0001))
                            ->groupBy('geometry');

                    })
                    ->withAssets($child_category_ids ? $child_category_ids : $asset_category->id)
                    ->groupBy('location_type_id', 'locations.locations.name','locations.locations.id')
                    ;
            }])
            ->get();

        if(!$location_type){
            
            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'location slug not found',
                status_code: 404
            );
        }

        if($wants_geojson){

            return StandardizeResponse::APIResponse(
                
                data: [
                        'asset_category' => $asset_category,
                        'location_type' => Location::getAssetsAsGeoJSON($location_type)
                    ]

                );

        }

        return StandardizeResponse::APIResponse(
            data: [
                'asset_category' => $asset_category,
                'location_type' => $location_type
            ]);

    }

    public function getAssetsByLocation(Request $request, $asset_category_slug, $location_type_slug, $location_id){

        $wants_geojson = $this->wantsGeoJSON($request);

        $asset_category = AssetCategory::defaultSelects()->where('slug',$asset_category_slug)->firstOrFail();

        $location_type = LocationType::defaultSelects()
            ->where('locations.location_types.slug', $location_type_slug)
            ->with(['locations' => function($query)use($asset_category, $location_id, $wants_geojson){
                $query
                    ->select('location_type_id', 'locations.locations.name')
                    ->selectRaw("count('assets.assets.*') as assets")
                    ->when($wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries','geometry', .0001)))
                    ->withAssets($asset_category->id)
                    ->where('locations.locations.id', $location_id)
                    ->groupBy('location_type_id', 'locations.locations.name', 'geometry')
                    ;
            }])
            ->get();

        if(!$location_type){

            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'location type slug not found',
                status_code: 404
            );
        }

        if(!$location_type){
        
            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'location slug not found',
                status_code: 404
            );
        }

        if($wants_geojson){

            return StandardizeResponse::APIResponse(
                
                data: [
                        'asset_category' => $asset_category,
                        'location_type' => Location::getAssetsAsGeoJSON($location_type)
                    ]

                );

        }


        return StandardizeResponse::APIResponse(
            data: [
                'asset_category' => $asset_category,
                'location_type' => $location_type
            ]);

    }


    public function getAssetsByCustomLocation(Request $request, $asset_category_slug){


        $asset_category = AssetCategory::defaultSelects()
            ->where('slug',$asset_category_slug)
            ->firstOrFail();

        if(!$asset_category){
            return StandardizeResponse::APIResponse(
                error_status: true,
                error_message: 'asset category slug not found',
                status_code: 404
            );
        }

        $assets = Asset::selectRaw('count(assets.assets.*)')
            ->where('assets.assets.asset_category_id', $asset_category->id)
            ->assetsByCustomLocationFilter($request->location)
            ->get();

        
        return StandardizeResponse::APIResponse(
            data: [
                'asset_category' => $asset_category, 
                'assets' => $assets
            ]
            );


    }

}
