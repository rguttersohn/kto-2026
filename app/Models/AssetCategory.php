<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Support\PostGIS;
use Illuminate\Database\Eloquent\Builder;

class AssetCategory extends Model
{   

    use HasFactory;
    
    protected $connection = 'supabase';

    protected $table = 'assets.asset_categories';

    protected $fillable = [
        'name',
        'slug'
    ];


    public function setNameAttribute($value)
    {
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function assets(){
        return $this->hasMany(Asset::class, 'asset_category_id', 'id');
    }


    #[Scope]

    protected function withAssetDetails(Builder $query, bool $wants_geojson = false){

        $query->with(['assets' => function($query)use($wants_geojson){

            $query
                ->select('description', 'asset_category_id')
                ->when($wants_geojson, fn($query)=>$query->selectRaw(PostGIS::getGeoJSON('assets.assets', 'location')));
        }]);
    }



}
