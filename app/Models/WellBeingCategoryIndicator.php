<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WellBeingCategoryIndicator extends Model
{
    protected $connection = 'supabase';

    protected $table = 'well_being_index.category_indicator';

    protected $fillabe = [
        'category_id',
        'indicator_id'
    ];

}
