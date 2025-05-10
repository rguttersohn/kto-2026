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
        'note'
    ];

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

    public function searchableAs()
    {
        return 'kto_indicators_dev';
    }

    #[Scope]
    protected function withDataDetails(
            Builder $query, 
            int | null $breakdown = null, 
            int | null $timeframe = null, 
            int | null $location = null,
            int | null $location_type = null,
            int | null $data_format = null
            ){

        return $query->with(['data' => function($query)use($breakdown, $timeframe, $location, $location_type, $data_format){
            return $query
                ->select('data.id','data', 'indicator_id', 'l.name as location','lt.name as location_type','timeframe', 'bk.name as breakdown_name', 'df.name as format')
                ->join('locations.locations as l', 'location_id', 'l.id')
                ->join('locations.location_types as lt', 'l.location_type_id', 'lt.id')
                ->join('indicators.data_formats as df', 'data_format_id', 'df.id')
                ->join('indicators.breakdowns as bk', 'breakdown_id', 'bk.id')
                ->when($breakdown,  fn($query)=>$query->where('breakdown_id', $breakdown))
                ->when($timeframe, fn($query)=>$query->where('timeframe', $timeframe))
                ->when($location, fn($query)=>$query->where('location_id', $location))
                ->when($location_type, fn($query)=>$query->where('location_type_id', $location_type))
                ->when($data_format, fn($query)=>$query->where('data_format_id', $data_format));
        }]);
    }

    #[Scope]
    protected function withAvailableFilters(Builder $query){

        return $query->with(['data' => function($query){
            
        return $query
                ->select('data.indicator_id')
                ->selectRaw("jsonb_agg(DISTINCT timeframe ORDER BY timeframe) AS timeframes")
                ->selectRaw("jsonb_agg(DISTINCT jsonb_build_object('id', bk.id, 'name', bk.name, 'parent_id', bk.parent_id)) AS breakdowns")
                ->selectRaw("jsonb_agg(DISTINCT jsonb_build_object('id', lt.id, 'name', lt.name)) AS location_types")
                ->selectRaw("jsonb_agg(DISTINCT jsonb_build_object('id',df.id, 'name', df.name)) AS data_formats")
                ->join('indicators.breakdowns as bk','data.breakdown_id', 'bk.id')
                ->join('locations.locations as l', 'data.location_id', 'l.id')
                ->join('locations.location_types as lt', 'l.location_type_id', 'lt.id')
                ->join('indicators.data_formats as df', 'data_format_id', 'df.id')
                ->groupBy('data.indicator_id');
        }]);

    }

    public static function formatFilters(Collection $filters_unformatted){
        

        $filters = array_map(function($filter){

            $timeframes =json_decode($filter['data'][0]['timeframes']);
            
            $location_types = json_decode($filter['data'][0]['location_types']);

            $data_formats = json_decode($filter['data'][0]['data_formats']);

            $breakdowns =json_decode($filter['data'][0]['breakdowns']);

            $breakdown_parent_ids = array_map(fn($breakdown)=>$breakdown->parent_id ?? $breakdown->id, $breakdowns);

            $breakdowns_w_parents = Breakdown::select('id', 'name')
                ->whereIn('id', $breakdown_parent_ids)
                ->with(['subBreakdowns' => fn($query)=>$query->select('id', 'name', 'parent_id')])
                ->get();

            $filter['data'] = 
                [
                    'breakdowns' => $breakdowns_w_parents,
                    'timeframes' => $timeframes,
                    'data_formats' => $data_formats,
                    'location_types' => $location_types
                ];

            return $filter;

        }, $filters_unformatted->toArray());

        return $filters;
        
    }
}
