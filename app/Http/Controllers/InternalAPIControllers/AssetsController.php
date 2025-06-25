<?php

namespace App\Http\Controllers\InternalAPIControllers;

use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Support\PostGIS;
use App\Models\LocationType;
use App\Support\StandardizeResponse;
use App\Models\Asset;
use App\Http\Controllers\Traits\HandlesAPIRequestOptions;
use App\Http\Resources\AssetCategoriesResource;
use App\Http\Resources\AssetCategoryResource;
use App\Http\Resources\AssetCategoryLocationTypeResource;
use App\Http\Resources\AssetCategoryCustomLocationResource;
use App\Http\Resources\AssetResource;
use App\Http\Resources\LocationTypeResource;
use App\Http\Controllers\Controller;

class AssetsController extends Controller
{

    use HandlesAPIRequestOptions;
    
    public function getAssetCategories(){

        $asset_categories = AssetCategory::select('id','name')
            ->whereNull('parent_id')
            ->get();

        return StandardizeResponse::internalAPIResponse(
            data: AssetCategoriesResource::collection($asset_categories)
        );

    }

    public function getAssetsByCategory(Request $request, $asset_category_id){
        
        $wants_geojson = $this->wantsGeoJSON($request);

        $subcategory = $this->subcategory($request);

        $asset_category = AssetCategory::defaultSelects()
            ->when(!$subcategory, fn($query)=>$query->with(['children'=> fn($query)=>$query->defaultSelects()]))
            ->where('id', $asset_category_id)
            ->first();

        if(!$asset_category){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 400
            );
        }

        $child_category_ids = $subcategory ?? $asset_category->children->pluck('id')->toArray();


        if(!$child_category_ids){

            $assets = Asset::assetsByCategoryID($wants_geojson, $asset_category->id)->get();

        } else {

            $assets = Asset::assetsByCategoryID($wants_geojson, $child_category_ids)->get();

        }

        $data = [
                'asset_category' => $asset_category,
                'assets' => $assets,
        ];

        return StandardizeResponse::internalAPIResponse(
            data: new AssetCategoryResource($data)
        );

        
    }

    public function getAssetsOnlyByCategory(Request $request, $asset_category_id){

        $wants_geojson = $this->wantsGeoJSON($request);

        $subcategory = $this->subcategory($request);

        $asset_category = AssetCategory::defaultSelects()
            ->when(!$subcategory, fn($query)=>$query->with(['children'=> fn($query)=>$query->defaultSelects()]))
            ->where('id', $asset_category_id)
            ->first();

        if(!$asset_category){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'id not found',
                status_code: 400
            );
        }

        $child_category_ids = $subcategory ?? $asset_category->children->pluck('id')->toArray();


        if(!$child_category_ids){

            $assets = Asset::assetsByCategoryID($wants_geojson, $asset_category->id)->get();

        } else {

            $assets = Asset::assetsByCategoryID($wants_geojson, $child_category_ids)->get();

        }

        return StandardizeResponse::internalAPIResponse(
            data: AssetResource::collection($assets)
        );
    }

    public function getAssetLocationTypes($asset_category_id){

        $asset_category = AssetCategory::defaultSelects()
            ->where('id', $asset_category_id)
            ->first();

        $location_types = LocationType::get();

        return StandardizeResponse::internalAPIResponse(
            data: [
                'asset_category' => new AssetCategoriesResource($asset_category),
                'location_types' => LocationTypeResource::collection($location_types)
            ]);

    }

    public function getAssetsByLocationType(Request $request, $asset_category_id, $location_type_id){

        $wants_geojson = $this->wantsGeoJSON($request);

        $subcategory = $this->subcategory($request);

        $asset_category = AssetCategory::defaultSelects()
            ->when(!$subcategory, fn($query)=>$query->with(['children' => fn($query)=>$query->defaultSelects()]))
            ->where('id', $asset_category_id)
            ->first();

        if(!$asset_category){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'asset category id not found',
                status_code: 400
            );
        }

        $child_category_ids = $subcategory ?? $asset_category->children->pluck('id')->toArray();
        
        $location_type = LocationType::defaultSelects()
            ->where('locations.location_types.id', $location_type_id)
            ->with(['locations' => function($query)use($asset_category, $wants_geojson, $child_category_ids){
                
                $query
                    ->select('locations.locations.id','locations.locations.location_type_id', 'locations.locations.name', 'locations.locations.fips', 'locations.locations.geopolitical_id')
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
            ->first();

        if(!$location_type){
            
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'location id not found',
                status_code: 400
            );
        }


        $data = [
            'asset_category' => $asset_category,
            'location_type' => $location_type
        ];

        return StandardizeResponse::internalAPIResponse(
            data: new AssetCategoryLocationTypeResource($data)
        );

    }

    public function getAssetsByLocation(Request $request, $asset_category_id, $location_type_id, $location_id){

        $wants_geojson = $this->wantsGeoJSON($request);

        $asset_category = AssetCategory::defaultSelects()->where('id',$asset_category_id)->first();

        $location_type = LocationType::defaultSelects()
            ->where('locations.location_types.id', $location_type_id)
            ->with(['locations' => function($query)use($asset_category, $location_id, $wants_geojson){
                $query
                    ->select('locations.locations.id','location_type_id', 'locations.locations.name','locations.locations.fips', 'locations.locations.geopolitical_id')
                    ->selectRaw("count('assets.assets.*') as assets")
                    ->when($wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries','geometry', .0001)))
                    ->withAssets($asset_category->id)
                    ->where('locations.locations.id', $location_id)
                    ->groupBy('locations.locations.id', 'location_type_id', 'locations.locations.name', 'geometry', 'locations.locations.geopolitical_id', 'locations.locations.fips')
                    ;
            }])
            ->first();

        if(!$location_type){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'location type slug not found',
                status_code: 400
            );
        }

        if(!$location_type){
        
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'location slug not found',
                status_code: 400
            );
        }

        $data = [
            'asset_category' => $asset_category,
            'location_type' => $location_type
        ];

        return StandardizeResponse::internalAPIResponse(
            data: new AssetCategoryLocationTypeResource($data)
        );

    }


    public function getAssetsByCustomLocation(Request $request, $asset_category_id){

        if(!$request->has('location')){

            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'missing required location param',
                status_code: 400
            );
        }

        $asset_category = AssetCategory::defaultSelects()
            ->where('id',$asset_category_id)
            ->first();

        if(!$asset_category){
            return StandardizeResponse::internalAPIResponse(
                error_status: true,
                error_message: 'asset category slug not found',
                status_code: 400
            );
        }

        $assets = Asset::selectRaw('count(assets.assets.*)')
            ->where('assets.assets.asset_category_id', $asset_category->id)
            ->assetsByCustomLocationFilter($request->location)
            ->first();
        
        $data = [
            'asset_category' => $asset_category, 
            'assets' => $assets
        ];

        return StandardizeResponse::internalAPIResponse(
            data: new AssetCategoryCustomLocationResource($data)
        );

    }

}
