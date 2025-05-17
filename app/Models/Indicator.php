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

    protected $dispatchesEvents = [
        'saved' => IndicatorSaved::class
    ];

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
            int | null $breakdown = null, 
            int | null $timeframe = null, 
            int | null $location = null,
            int | null $location_type = null,
            int | null $data_format = null,
            int $limit = 3000,
            int $offset = 0,
            bool $wants_geojson = false
            ){
            
        $enforced_limit = $limit <= 3000 ? $limit : 3000; 
        
        return $query->with(['data' => function($query)use($breakdown, $timeframe, $location, $location_type, $data_format, $offset, $enforced_limit, $wants_geojson){
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
                ->when($wants_geojson, function($query){
                    return $query->join('locations.geometries as geo', 'l.id', 'geo.location_id')
                    ->selectRaw(PostGIS::getSimplifiedGeoJSON('geo', 'geometry', .001));
                })
                ->when($breakdown,  fn($query)=>$query->where('breakdown_id', $breakdown))
                ->when($timeframe, fn($query)=>$query->where('timeframe', $timeframe))
                ->when($location, fn($query)=>$query->where('data.location_id', $location))
                ->when($location_type, fn($query)=>$query->where('location_type_id', $location_type))
                ->when($data_format, fn($query)=>$query->where('data_format_id', $data_format))
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

    public static function formatFilters(Collection $filters_unformatted):array{
        
        $filters_array = $filters_unformatted->toArray();

        $filter_ids_string = $filters_array[0]['data'][0];
        
        $filter_ids_array = array_map(function($ids){

            return Str::of($ids)
                ->trim('{}')
                ->explode(',')
                ->map(fn ($val) => (int) $val)
                ->toArray();

        }, $filter_ids_string);
        
        return [
            'id' => $filters_array[0]['id'],
            'name' => $filters_array[0]['name'],
            'slug' => $filters_array[0]['slug'],
            'data' => [
                'timeframes' => $filter_ids_array['timeframes'],
                'breakdowns' => Breakdown::select('name', 'slug', 'id')
                    ->whereIn('id', $filter_ids_array['breakdowns'])
                    ->with('subBreakdowns:id,name,parent_id')
                        ->get()->toArray(),
                'location_types' => LocationType::select('name','slug','id')
                    ->whereIn('id', $filter_ids_array['location_types'])
                    ->get()->toArray(),
                'data_formats' => DataFormat::select('name', 'id')->whereIn('id', $filter_ids_array['data_formats'])->get()->toArray()
            ]
    ];
  
    }

    public static function getDataAsGeoJSON(Collection $indicator){

        $indicator_array = $indicator->toArray();

        $geojson = array_map(function($indicator){
            
            return [
                'id' => $indicator['id'],
                'name' => $indicator['name'],
                'slug' => $indicator['slug'],
                'data' => [
                    'type' => 'FeatureCollection',
                    'features' => array_map(function($d){
                      
                        return [
                            'type' => 'Feature',
                            'geometry' => json_decode($d['geometry']),
                            'properties' => array_filter($d, fn($_d)=>$_d !== 'geometry', ARRAY_FILTER_USE_KEY)
                        ];

                    }, $indicator['data'])
                ]
            ];
        
        },$indicator_array);


        return $geojson;

    }
}
