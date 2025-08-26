<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;

class WellBeingDomainIndicator extends Model
{
    use Filterable;

    protected $connection = 'supabase';

    protected $table = 'well_being_index.domain_indicator';

    protected $fillable = [
        'domain_id',
        'indicator_id'
    ];

    protected $filter_whitelist = [
        'domain_id', 
        'indicator_id', 
        'timeframe', 
        'location', 
        'location_type'
    ];

    protected $filter_aliases = [
        'domain' => 'domain_id',
        'indicator' => 'indicator_id',
        'year' => 'timeframe',
        'location' => 'location_id',
        'location_type' => 'location_type_id'
    ];


    public function domain():BelongsTo{

        return $this->belongsTo(Domain::class);
    }

    public function indicator():BelongsTo{

        return $this->belongsTo(Indicator::class);
    }

}
