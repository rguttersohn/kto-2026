<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorMeta extends Model
{

    protected $table = 'indicators.meta';

    protected $fillable = [
        'indicator_id',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'og_image'
    ];

}
