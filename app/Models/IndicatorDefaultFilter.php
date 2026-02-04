<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\IndicatorFilterTypes;

class IndicatorDefaultFilter extends Model
{   

    protected $table = 'indicators.indicator_default_filters';

    protected $casts = [
        'filter_type' => IndicatorFilterTypes::class,
    ];
    
    protected $fillable = [
        'indicator_id', 
        'filter_type', 
        'default_value_id'
    ];

}
