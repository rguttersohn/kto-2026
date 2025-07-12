<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Support\PostGIS;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\ValidLocationScope;
use Illuminate\Support\Facades\DB;


#[ScopedBy([ValidLocationScope::class])]

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
        'valid_starting_on',
        'valid_ending_on'
    ];

    public function geometry()
    {
        return $this->hasMany(Geometry::class, 'location_id','id');
    }

    public function data(){

        return $this->hasMany(IndicatorData::class);
    }

    public function indicators()
    {
        return $this->hasManyThrough(
            Indicator::class,
            IndicatorData::class,
            'location_id', 
            'id',          
            'id',           
            'indicator_id'
            
        )->distinct();
    }


    #[Scope]
    protected function withIndicators(Builder $query){

        return $query->with(['data' => function($query){

            return $query->selectRaw('DISTINCT data.indicator_id as id, data.location_id, ind.name')
                ->join('indicators.indicators as ind', 'data.indicator_id', 'ind.id');
            
        }]);
            
    }

    #[Scope]

    protected function withIndicator(Builder $query, $indicator_id){

        return $query->with(['data' => function($query)use($indicator_id){

            return $query->selectRaw('DISTINCT data.indicator_id as id, data.location_id, ind.name, ind.definition, ind.source, ind.note')
                ->join('indicators.indicators as ind', 'data.indicator_id', 'ind.id')
                ->where('ind.id', $indicator_id);
            
        }]);
    }

    #[Scope]

    protected function withIndicatorData(
            Builder $query, 
            $indicator_id,
            int $limit = 3000,
            int $offset = 0,
            bool $wants_geojson = false,
            array | null $filters = null,
            array | null $sorts = null
            ){
        
        return $query->with(['data' => function($query) use($limit, $offset, $wants_geojson, $filters, $sorts, $indicator_id){

            return $query->withDetails(
                limit: $limit,
                offset: $offset,
                wants_geojson: $wants_geojson,
                filters: $filters,
                sorts: $sorts
            )
            ->where('indicator_id', $indicator_id);

        }]);
    }

    #[Scope]

    protected function withAssets(Builder $query, array $asset_category_ids){

        return $query->crossJoin(DB::raw('(SELECT unnest(array[' . implode(',', $asset_category_ids) . ']) as asset_category_id) as cross_join'))
            ->join('locations.geometries','locations.geometries.location_id', 'locations.locations.id')
            ->leftJoin('assets.assets', function($join)use($asset_category_ids){
                    
                $join
                    ->where(...PostGIS::isGeometryWithin('assets.assets.geometry', 'locations.geometries.geometry'))
                    ->when($asset_category_ids, function($query)use($asset_category_ids){
                       
                        if(is_array($asset_category_ids)){

                            $query->whereIn('assets.assets.asset_category_id', $asset_category_ids);
                            
                        } else {

                            $query->where('assets.assets.asset_category_id', $asset_category_ids);

                        }

                    })
                ;

            })
            ->join('assets.asset_categories', 'cross_join.asset_category_id', 'assets.asset_categories.id')
            ;
    }


}
