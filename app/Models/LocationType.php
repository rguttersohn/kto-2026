<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\LocationTypeClassification;
use App\Enums\LocationScopes;
use Illuminate\Support\Str;

class LocationType extends Model
{   

    protected $connection = 'supabase';
    protected $table = 'location_types';

    protected $fillable = [
        'name',
        'plural_name',
        'classification',
        'slug',
        'scope'
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
    public function setNameAttribute($value)
    {   
        if(isset($this->attributes['slug'])){
            return;
        }
        
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
