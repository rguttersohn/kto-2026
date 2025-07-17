<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellBeingDomainIndicator extends Model
{
    protected $connection = 'supabase';

    protected $table = 'well_being_index.domain_indicator';

    protected $fillable = [
        'domain_id',
        'indicator_id'
    ];


    public function domain():BelongsTo{

        return $this->belongsTo(Domain::class);
    }

    public function indicator():BelongsTo{

        return $this->belongsTo(Indicator::class);
    }

}
