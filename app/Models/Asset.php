<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use App\Support\GeoJSON;

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
