<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use App\Events\IndicatorSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Models\Data;
use Illuminate\Support\Collection;
use App\Models\Breakdown;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Support\PostGIS;

#[ScopedBy([PublishedScope::class])]

class Indicator extends Model
{
    use Searchable;

    protected $connection = 'supabase';

    protected $table = 'indicators.indicators';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'name',
        'slug',
        'category_id',
        'definition',
        'source',
        'note',
        'is_published'
    ];

    public function searchableAs()
    {
        return 'kto_indicators_dev';
    }

    // protected $dispatchesEvents = [
    //     'saved' => IndicatorSaved::class
    // ];

    public function data(){
        return $this->hasMany(Data::class);
    }

    public function setNameAttribute($value)
    {
        if (isset($this->attributes['slug'])) {
            return;
        }

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($this->attributes['name']);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    #[Scope]
    protected function withDataDetails(
            Builder $query, 
            int $limit = 3000,
            int $offset = 0,
            bool $wants_geojson = false,
            array | null $filters = null,
            array | null $sorts = null
            ){
            
        $enforced_limit = $limit <= 3000 ? $limit : 3000; 
        
        return $query->with(['data' => function($query)use($offset, $enforced_limit, $wants_geojson, $filters, $sorts){
            return $query
                ->select(
                        'data', 
                        'indicator_id', 
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
        }]);
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
