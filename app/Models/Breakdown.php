<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Breakdown extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.breakdowns';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'slug',
        'parent_id',
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
        return $this->belongsTo(Breakdown::class, 'parent_id', 'id');
    }

    public function subBreakdowns()
    {
        return $this->hasMany(Breakdown::class, 'parent_id', 'id');
    }
    
}
