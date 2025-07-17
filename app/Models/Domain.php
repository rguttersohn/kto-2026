<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $connection = 'supabase';

    protected $table = 'domains.domains';

    protected $fillable = [
        'id',
        'name',
        'definition',
        'is_rankable'
    ];


    public function indicatorCategories():HasMany{

        return $this->hasMany(IndicatorCategory::class);
    
    }

     public function indicatorsInRanking(){

        return $this->hasManyThrough(Indicator::class, WellBeingCategoryIndicator::class, 'category_id', 'id', 'id', 'indicator_id');
    }
}
