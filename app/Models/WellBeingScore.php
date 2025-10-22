<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\Filterable;
use App\Models\Traits\HasAdminPublishPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\PublishedScope;

#[ScopedBy(PublishedScope::class)]

class WellBeingScore extends Model
{
    use Filterable, HasAdminPublishPolicy, HasFactory;

    protected $connection = 'supabase';

    protected $table = 'well_being_index.scores';

    protected $fillable = [
        'indicator_category_id',
        'timeframe',
        'score',
        'location_id',
        'import_id'
    ];

    protected array $filter_aliases = [
        'domain' => 'domain_id',
        'location' => 'location_id',
        'location_type' => 'location_type_id'
    ];

    protected array $filter_whitelist = [
       'domain_id',
       'location_id',
       'timeframe',
       'score',
       'location_type_id',
       'is_published'
    ];

    public function domain():BelongsTo{

        return $this->belongsTo(Domain::class);
    }


    public function category():BelongsTo{

        return $this->belongsTo(Indicator::class);
    }

    public function location():BelongsTo{

        return $this->belongsTo(Location::class);
    }

    public function import():BelongsTo{

        return $this->belongsTo(Import::class);
    }

}
