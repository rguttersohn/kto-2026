<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use App\Events\IndicatorSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Models\IndicatorData;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use App\Policies\IndicatorPolicy;
use App\Models\Traits\HasAdminPublishPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ScopedBy([PublishedScope::class])]
#[UsePolicy(IndicatorPolicy::class)]

class Indicator extends Model
{
    use Searchable, HasAdminPublishPolicy, HasFactory;

    protected $connection = 'supabase';

    protected $table = 'indicators.indicators';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'category_id',
        'definition',
        'source',
        'note',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    public function searchableAs()
    {
        return 'kto_indicators_dev';
    }

    protected $dispatchesEvents = [
        'saved' => IndicatorSaved::class
    ];

    public function data(){
        return $this->hasMany(IndicatorData::class, 'indicator_id');
    }

    public function category()
    {
        return $this->belongsTo(IndicatorCategory::class, 'category_id', 'id');
    }

   
    #[Scope]
    protected function withAvailableFilters(Builder $query){
        
        return $query->with(['data' => function($query){
            
            return $query    
                    ->select('data.indicator_id')
                    ->selectRaw('array_agg(DISTINCT timeframe ORDER BY timeframe) as timeframes')
                    ->selectRaw('array_agg(DISTINCT lt.id) AS location_types')
                    ->selectRaw('array_agg(DISTINCT df.id) AS data_formats')
                    ->selectRaw('array_agg(DISTINCT COALESCE(bk_parent.id, bk_child.id)) as breakdowns')
                    ->join('locations.locations as l', 'data.location_id', 'l.id')
                    ->join('locations.location_types as lt', 'l.location_type_id', 'lt.id')
                    ->join('indicators.data_formats as df', 'data.data_format_id', 'df.id')
                    ->join('indicators.breakdowns as bk_child', 'data.breakdown_id', 'bk_child.id')
                    ->leftJoin('indicators.breakdowns as bk_parent', 'bk_child.parent_id', 'bk_parent.id')
                    ->groupBy('data.indicator_id');

        }]);

    }

}
