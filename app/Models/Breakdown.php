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
        'breakdown_type_id',
    ];

    public function setNameAttribute($value)
    {   
        if(isset($this->attributes['slug'])){
            return;
        }
        
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }


    public function parent()
    {
        return $this->belongsTo(BreakdownType::class, 'breakdown_type_id', 'id');
    }

    
}
