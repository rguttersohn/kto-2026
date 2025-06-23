<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\PublishedScope;
use App\Models\Traits\Filterable;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Support\PostGIS;

#[ScopedBy([PublishedScope::class])]

class DataIndicator extends Model
{   
    use HasFactory, Filterable, Sortable;

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
        'location_type' => 'location_type_id',
        'format' => 'data_format_id',
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

    protected array $sort_aliases = [
        'location_type' => 'location_type_id',
        'format' => 'data_format_id',
        'breakdown' => 'breakdown_id',
        'location' => 'location_id'
    ];

    protected array $sort_whitelist = [
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

    #[Scope]
    protected function withDetails(
            Builder $query, 
            int $limit = 3000,
            int $offset = 0,
            bool $wants_geojson = false,
            array | null $filters = null,
            array | null $sorts = null
            ):Builder{
            
        $enforced_limit = $limit <= 3000 ? $limit : 3000; 
                
        $query
            ->select(
                    'data',
                    'indicator_id' ,
                    'l.id as location_id', 
                    'l.name as location',
                    'lt.name as location_type',
                    'timeframe', 
                    'bk.name as breakdown_name',
                    'df.name as format', 
                    )
            ->join('locations.locations as l', 'data.location_id', 'l.id')
            ->join('locations.location_types as lt', 'l.location_type_id', 'lt.id')
            ->join('indicators.data_formats as df', 'data_format_id', 'df.id')
            ->join('indicators.breakdowns as bk', 'breakdown_id', 'bk.id')
            ->when($filters, fn($query)=>$query->filter($filters))
            ->when($sorts, fn($query)=>$query->sort($sorts))
            ->when($wants_geojson, function($query) {
                return $query->join('locations.geometries as geo', function($join) {
                        $join->on('l.id', '=', 'geo.location_id')
                                ->whereNull('geo.valid_ending_on');
                    })
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo','geometry', .0001));
            })                
            ->limit($enforced_limit)
            ->offset($offset)
            ;

        return $query;
        
    }

}
