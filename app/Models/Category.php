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
        'parent_id',
    ];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'category_id', 'id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function indicatorsInRanking(){

        return $this->hasManyThrough(Indicator::class, WellBeingCategoryIndicator::class, 'category_id', 'id', 'id', 'indicator_id');
    }


}
