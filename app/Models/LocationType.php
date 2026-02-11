<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LocationTypeClassification;
use App\Enums\LocationScopes;
use App\Models\Scopes\LocalScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Traits\Filterable;

#[ScopedBy(LocalScope::class)]

class LocationType extends Model
{   
    use Filterable;

    protected $connection = 'supabase';
    protected $table = 'location_types';

    protected $fillable = [
        'name',
        'plural_name',
        'classification',
        'scope',
        'is_rankable',
        'has_community_profile'
    ];

    protected $casts = [
        'classification' => LocationTypeClassification::class,
        'scope' => LocationScopes::class,
        'is_rankable' => 'boolean'
    ];


    /**
     * 
     * filter stuff
     * 
     */

    protected array $filter_whitelist = [
        'has_community_profile'
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

    
}
