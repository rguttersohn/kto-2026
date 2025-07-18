<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LocationTypeClassification;
use App\Enums\LocationScopes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;


class LocationType extends Model
{   

    protected $connection = 'supabase';
    protected $table = 'location_types';

    protected $fillable = [
        'name',
        'plural_name',
        'classification',
        'scope',
        'has_ranking'
    ];

    protected $casts = [
        'classification' => LocationTypeClassification::class,
        'scope' => LocationScopes::class
    ];

    public function locations()
    {
        return $this->hasMany(Location::class, 'location_type_id', 'id');
    }

    public function geometries()
    {
        return $this->hasManyThrough(
            Geometry::class,
            Location::class,
            'location_type_id',
            'location_id',
            'id',
            'id'
        );
    }


    #[Scope]
    protected function defaultSelects(Builder $query){
        return $query->select('id', 'name','plural_name','classification', 'scope');
    }
    
}
