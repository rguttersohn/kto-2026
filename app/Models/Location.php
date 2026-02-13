<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\ValidLocationScope;
use App\Models\Scopes\UninhabitedLocationScope;


#[ScopedBy([ValidLocationScope::class, UninhabitedLocationScope::class])]

class Location extends Model
{
    
    protected $connection = 'supabase';

    protected $table = 'locations.locations';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'fips',
        'name',
        'location_type_id',
        'district_id',
        'legacy_district_id',
        'valid_starting_on',
        'valid_ending_on',
        'is_uninhabited'
    ];

    public function geometry()
    {
        return $this->hasMany(Geometry::class, 'location_id','id');
    }

    public function data(){

        return $this->hasMany(IndicatorData::class);
    }

    public function locationType(){

        return $this->belongsTo(LocationType::class, 'location_type_id', 'id');

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

            return $query->selectRaw('DISTINCT data.indicator_id as id, data.location_id, ind.name as name')
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


}
