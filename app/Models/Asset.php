<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Support\PostGIS;
use App\Models\Traits\Filterable;


class Asset extends Model
{
    use HasFactory, Filterable;
    
    protected $connection = 'supabase';

    protected $table = 'assets.assets';

    protected $fillable = [
        'description',
        'location',
        'asset_category_id'
    ];

    protected $filter_aliases = [
        'category' => 'asset_category_id'
    ];

    protected $filter_whitelist = [
        'asset_category_id'
    ];
   
    public function assetCategory(){

        return $this->belongsTo(AssetCategory::class);
    }


    #[Scope]

    protected function assetsByCategoryID(Builder $query, array $filters, bool $wants_geojson){

        $query
            ->filter($filters)
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
