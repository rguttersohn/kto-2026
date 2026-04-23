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
use App\Policies\IndicatorDataPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use App\Models\Traits\HasAdminPublishPolicy;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Support\Facades\Cache;


#[ScopedBy([PublishedScope::class])]
#[UsePolicy(IndicatorDataPolicy::class)]

class IndicatorData extends Model
{   
    use HasFactory, Filterable, Sortable, HasAdminPublishPolicy;

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
        'created_at',
        'import_id'
    ];

    protected $casts = [
        'data' => 'float',
        'is_published' => 'boolean'
    ];

    protected array $filter_aliases = [
        'location_type' => 'locations.location_types.id',
        'format' => 'data_format_id',
        'breakdown' => 'breakdown_id',
        'location' => 'indicators.data.location_id',
        'year' => 'timeframe',
        'breakdown_parent'  => 'indicators.breakdowns.parent_id'
    ];

    protected array $filter_whitelist = [
        'data',
        'data_format_id',
        'breakdown_id',
        'timeframe',
        'locations.location_types.id',
        'indicators.data.location_id',
        'indicators.breakdowns.parent_id'
    ];

    protected array $sort_aliases = [
        'location_type' => 'location_type_id',
        'format' => 'data_format_id',
        'breakdown' => 'breakdown_id',
        'location' => 'indicators.data.location_id'
    ];

    protected array $sort_whitelist = [
        'data',
        'data_format_id',
        'breakdown_id',
        'timeframe',
        'location_type_id',
        'indicators.data.location_id'
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

    public function import(){
        
        return $this->belongsTo(Import::class, 'import_id', 'id');
    }

    #[Scope]
    protected function joinLocation(Builder $query){

        return $query
            ->join('locations.locations', 'indicators.data.location_id', 'locations.locations.id')
            ->join('locations.location_types', 'locations.locations.location_type_id', 'locations.location_types.id')
            ->addSelect('locations.locations.name as location_name')
            ->addSelect('locations.locations.id as location_id')
            ->addSelect('locations.location_types.name as location_type_name')
            ->addSelect('locations.location_types.id as location_type_id');
    }

    #[Scope]
    protected function joinDataFormat(Builder $query){

        return $query
            ->join('indicators.data_formats', 'indicators.data.data_format_id', 'indicators.data_formats.id')
            ->addSelect('indicators.data_formats.name as format_name')
            ->addSelect('indicators.data_formats.id as format_id');
    }

    #[Scope]
    protected function joinBreakdown(Builder $query){

        return $query
            ->join('indicators.breakdowns', 'indicators.data.breakdown_id', 'indicators.breakdowns.id')
            ->join('indicators.breakdowns as breakdown_parents', 'indicators.breakdowns.parent_id', 'breakdown_parents.id')
            ->addSelect('indicators.breakdowns.name as breakdown_name')
            ->addSelect('indicators.breakdowns.id as breakdown_id')
            ->addSelect('breakdown_parents.name as breakdown_parent_name')
            ->addSelect('breakdown_parents.id as breakdown_parent_id');
    }

    #[Scope]
    protected function joinGeometry(Builder $query){

            return $query->join('locations.geometries', function($join) {
                        $join->on('locations.locations.id', '=', 'locations.geometries.location_id')
                                ->whereNull('locations.geometries.valid_ending_on');
                    })
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries','geometry'));
    }

    #[Scope]
    protected function joinAll(Builder $query, bool $wants_geojson = false){

        return $query
            ->joinLocation()
            ->joinDataFormat()
            ->joinBreakdown()
            ->when($wants_geojson, fn($query)=>$query->joinGeometry());
    }

    #[Scope]
    protected function addLimit(Builder $query, $limit){

        $enforced_limit = $limit <= 3000 ? $limit : 3000;

        return $query->limit($enforced_limit);

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
                    'indicators.data.id as id',
                    'indicators.data.data as data',
                    'indicators.data.indicator_id as indicator_id', 
                    'indicators.data.location_id as location_id', 
                    'locations.locations.name as location',
                    'locations.location_types.id  as location_type_id',
                    'locations.location_types.name as location_type',
                    'indicators.data.timeframe as timeframe',
                    'indicators.breakdowns.name as breakdown_name',
                    'indicators.breakdowns.id as breakdown_id',
                    'indicators.data_formats.name as format', 
                    'indicators.data_formats.id as format_id'
                    )
            ->join('locations.locations', 'indicators.data.location_id', 'locations.locations.id')
            ->join('locations.location_types', 'locations.locations.location_type_id', 'locations.location_types.id')
            ->join('indicators.data_formats', 'indicators.data.data_format_id', 'indicators.data_formats.id')
            ->join('indicators.breakdowns', 'indicators.data.breakdown_id', 'indicators.breakdowns.id')
            ->when($filters, fn($query)=>$query->filter($filters))
            ->when($sorts, fn($query)=>$query->sort($sorts))
            ->when($wants_geojson, function($query) {
                return $query->join('locations.geometries', function($join) {
                        $join->on('locations.locations.id', '=', 'locations.geometries.location_id')
                                ->whereNull('locations.geometries.valid_ending_on');
                    })
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries','geometry'));
            })                
            ->limit($enforced_limit)
            ->offset($offset)
            ;

        return $query;
        
    }

    #[Scope]
    protected function withFilterIDs(Builder $query){
        
        return $query    
                ->select('data.indicator_id')
                ->selectRaw('array_agg(DISTINCT timeframe ORDER BY timeframe) as timeframe')
                ->selectRaw('array_agg(DISTINCT lt.id) AS location_type')
                ->selectRaw('array_agg(DISTINCT df.id) AS format')
                ->selectRaw('array_agg(DISTINCT bk.id) as breakdown')
                ->join('locations.locations as l', 'data.location_id', 'l.id')
                ->join('locations.location_types as lt', 'l.location_type_id', 'lt.id')
                ->join('indicators.data_formats as df', 'data.data_format_id', 'df.id')
                ->join('indicators.breakdowns as bk', 'data.breakdown_id', 'bk.id')
                ->groupBy('data.indicator_id');

    }

    #[Scope]
    protected function withDetailsWithOutLimit(
            Builder $query, 
            bool $wants_geojson = false,
            array | null $filters = null,
            array | null $sorts = null
            ):Builder{
                            
        $query
            ->select(
                    'indicators.data.id as id',
                    'indicators.data.data as data',
                    'indicators.data.indicator_id as indicator_id', 
                    'indicators.data.location_id as location_id', 
                    'locations.locations.name as location',
                    'locations.location_types.id  as location_type_id',
                    'locations.location_types.name as location_type',
                    'indicators.data.timeframe as timeframe',
                    'indicators.breakdowns.name as breakdown_name',
                    'indicators.breakdowns.id as breakdown_id',
                    'indicators.data_formats.name as format', 
                    'indicators.data_formats.id as format_id'
                    )
            ->join('locations.locations', 'indicators.data.location_id', 'locations.locations.id')
            ->join('locations.location_types', 'locations.locations.location_type_id', 'locations.location_types.id')
            ->join('indicators.data_formats', 'indicators.data.data_format_id', 'indicators.data_formats.id')
            ->join('indicators.breakdowns', 'indicators.data.breakdown_id', 'indicators.breakdowns.id')
            ->when($filters, fn($query)=>$query->filter($filters))
            ->when($sorts, fn($query)=>$query->sort($sorts))
            ->when($wants_geojson, function($query) {
                return $query->join('locations.geometries', function($join) {
                        $join->on('locations.locations.id', '=', 'locations.geometries.location_id')
                                ->whereNull('locations.geometries.valid_ending_on');
                    })
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('locations.geometries','geometry'));
            });

        return $query;
        
    }

    #[Scope]
    protected function forCounting (
            Builder $query, 
            array | null $filters = null,
            ):Builder{            
        $query
            ->select('*')
            ->join('locations as l','location_id', 'l.id')
            ->join('location_types as lt', 'l.location_type_id', 'lt.id')
            ->when($filters, fn($query)=>$query->filter($filters));

        return $query;
        
    }

    #[Boot]
    protected static function emptyCache(){

        static::saved(function($model){

            Cache::tags("indicator_$model->indicator_id")->flush();
        
        });
    }

}
