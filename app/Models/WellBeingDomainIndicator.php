<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellBeingDomainIndicator extends Model
{
    protected $connection = 'supabase';

    protected $table = 'well_being_index.domain_indicator';

    protected $fillable = [
        'category_id',
        'indicator_id'
    ];

}
