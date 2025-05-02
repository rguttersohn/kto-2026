<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DataFormat extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.data_formats';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'slug',
    ];

    public function setNameAttribute($value)
    {
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function data()
    {
        return $this->hasMany(Datum::class, 'data_format_id', 'id');
    }
}
