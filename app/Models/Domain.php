<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function indicatorsInRanking(){

        return $this->hasManyThrough(Indicator::class, WellBeingDomainIndicator::class, 'domain_id', 'id', 'id', 'indicator_id');
    }
}
