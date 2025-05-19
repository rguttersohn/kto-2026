<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Support\PostGIS;
use Illuminate\Database\Eloquent\Collection;
use App\Support\GeoJSON;


class Location extends Model
{
    protected $connection = 'supabase';

    protected $table = 'locations';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'fips',
        'name',
        'location_type_id',
        'geopolitical_id',
    ];

    public function geometry()
    {
        return $this->hasMany(Geometry::class, 'location_id','id');
    }

    public function data(){

        return $this->hasMany(Data::class);
    }


    #[Scope]
    protected function withIndicators(Builder $query){

        return $query->with(['data' => function($query){

            return $query->selectRaw('DISTINCT data.indicator_id, data.location_id, ind.name, ind.slug')
                ->join('indicators.indicators as ind', 'data.indicator_id', 'ind.id');
            
        }]);
            
    }

    #[Scope]

    protected function withIndicator(Builder $query, $indicator_slug){

        return $query->with(['data' => function($query)use($indicator_slug){

            return $query->selectRaw('DISTINCT data.indicator_id, data.location_id, ind.name, ind.slug, ind.definition, ind.source, ind.note')
                ->join('indicators.indicators as ind', 'data.indicator_id', 'ind.id')
                ->where('ind.slug', $indicator_slug);
            
        }]);
    }

    #[Scope]

    protected function withAssets(Builder $query, int | null $asset_category_id = null){

        return $query
                ->join('locations.geometries','locations.geometries.location_id', 'locations.locations.id')
                ->leftJoin('assets.assets', function($join)use($asset_category_id){
                    
                    $join
                        ->where(...PostGIS::isGeometryWithin('assets.assets.location', 'locations.geometries.geometry'))
                        ->when($asset_category_id, fn($query)=>$query->where('assets.assets.asset_category_id', $asset_category_id));

                });
    }

    public static function getAssetsAsGeoJSON(Collection $asset_category){

        $asset_category_array = $asset_category->toArray();

        $geojson = array_map(function($asset_category){
            
            return [
                'id' => $asset_category['id'],
                'name' => $asset_category['name'],
                'slug' => $asset_category['slug'],
                'locations' => GeoJSON::getGeoJSON($asset_category['locations'], 'geometry')
            ];
        
        },$asset_category_array);

        return $geojson;

    }


}
