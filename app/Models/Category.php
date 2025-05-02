<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $connection = 'supabase';

    protected $table = 'indicators.categories';

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
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'category_id', 'id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }
    
    public function categories()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

}
