<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Domain extends Model
{
    protected $connection = 'supabase';

    protected $table = 'domains.domains';

    protected $casts = [
        'is_rankable' => 'boolean'
    ];

    protected $fillable = [
        'id',
        'name',
        'definition',
        'is_rankable'
    ];


    public function indicatorCategories():HasMany{

        return $this->hasMany(IndicatorCategory::class);
    
    }

    public function indicators():HasManyThrough{

        return $this->hasManyThrough(Indicator::class, IndicatorCategory::class, 'domain_id', 'category_id');

    }

   
}
