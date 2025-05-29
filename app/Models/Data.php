<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\PublishedScope;
use App\Models\Traits\Filterable;

#[ScopedBy([PublishedScope::class])]

class Data extends Model
{   
    use HasFactory, Filterable;

    protected $connection = 'supabase';

    protected $table = 'indicators.data';

    protected $fillable = [
        'data',
        'data_format_id',
        'timeframe',
        'location_id',
        'breakdown_id',
        'indicator_id',
        'is_published',
        'updated_at',
        'created_at'
    ];

    protected $casts = [
        'data' => 'float'
    ];

    protected array $filter_aliases = [
        'location-type' => 'location_type_id',
        'data-format' => 'data_format_id',
        'breakdown' => 'breakdown_id',
        'location' => 'location_id'
        
    ];

    protected array $filter_whitelist = [
        'data',
        'data_format_id',
        'breakdown_id',
        'timeframe',
        'location_type_id',
        'location_id'
    ];

    public function indicator(){

        return $this->belongsTo(Indicator::class);
    }

    public function location(){

        return $this->belongsTo(Location::class);

    }
    
    public function breakdown(){

        return $this->belongsTo(Breakdown::class);

    }

    public function dataFormat(){

        return $this->belongsTo(DataFormat::class, 'data_format_id', 'id');

    }

}
