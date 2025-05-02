<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BreakdownType extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.breakdown_types';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'slug',
    ];

    public function setNameAttribute($value)
    {   
        if(isset($this->attributes['slug'])){
            return;
        }
        
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function breakdowns()
    {
        return $this->hasMany(Breakdown::class, 'breakdown_type_id', 'id');
    }
}