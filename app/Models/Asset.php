<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use App\Support\GeoJSON;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Support\PostGIS;

class Asset extends Model
{
    use HasFactory;
    
    protected $connection = 'supabase';

    protected $table = 'assets.assets';

    protected $fillable = [
        'description',
        'location',
        'asset_category_id'
    ];

   
    public function AssetCategory(){

        return $this->belongsTo(AssetCategory::class);
    }


    #[Scope]

    protected function withCustomLocationFilter(Builder $query, string $custom_location){

        $location = 'assets.assets.location';

        $custom_location = PostGIS::getGeoFromText($custom_location);

        $query->where(...PostGIS::isGeometryWithin($location, $custom_location));
       
    }


    public static function getAssetsAsGeoJSON(Collection $asset_category){

        $asset_category_array = $asset_category->toArray();

        $geojson = array_map(function($asset_category){
            
            return [
                'id' => $asset_category['id'],
                'name' => $asset_category['name'],
                'slug' => $asset_category['slug'],
                'data' => GeoJSON::getGeoJSON($asset_category['assets'], 'location')
            ];
        
        },$asset_category_array);

        return $geojson;

    }


}
