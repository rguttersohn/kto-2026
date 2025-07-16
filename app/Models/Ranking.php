<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    protected $connection = 'supabase';

    protected $table = 'well_being_index.rankings';

    protected $fillable = [
        'indicator_category_id',
        'year',
        'score',
        'location_id'
    ];


    public function category():BelongsTo{

        return $this->belongsTo(Indicator::class);
    }

    public function location():BelongsTo{

        return $this->belongsTo(Location::class);
    }
}
