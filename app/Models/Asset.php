<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

   
    public function assetCategory(){

        return $this->belongsTo(AssetCategory::class);
    }


    #[Scope]

    protected function assetsByCategoryID(Builder $query, bool $wants_geojson, int | array | null $category_ids){

        $query
            ->select('description','asset_category_id')
            ->when($category_ids, function($query)use($category_ids){
                    
                if(gettype($category_ids) === 'integer'){

                    $query->where('asset_category_id', $category_ids);

                } else {

                    $query->whereIn('asset_category_id', $category_ids);

                }

            })
            ->when(!$wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getLongLatFromPoint('assets.assets', 'location')))
            ->when($wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getGeoJSON('assets.assets', 'location')));
    }


    #[Scope]

    protected function assetsByCustomLocationFilter(Builder $query, string $custom_location){
        
        $location = 'assets.assets.location';

        $custom_location = PostGIS::getGeoFromText($custom_location);

        $query->where(...PostGIS::isGeometryWithin($location, $custom_location));
       
    }

}
