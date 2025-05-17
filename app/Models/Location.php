<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;


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


}
