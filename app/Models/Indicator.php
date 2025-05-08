<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use App\Events\IndicatorSaved;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;



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

        $query->with(['data' => function($query)use($breakdown, $timeframe, $location, $location_type, $data_format){
            return $query
                ->select('data.id','data', 'l.name as location', 'location_id','indicator_id', 'timeframe', 'bk.name as breakdown_name','breakdown_id', 'df.name as format')
                ->join('locations.locations as l', 'location_id', 'l.id')
                ->join('indicators.data_formats as df', 'data_format_id', 'df.id')
                ->join('indicators.breakdowns as bk', 'breakdown_id', 'bk.id')
                ->where('breakdown_id', $breakdown)
                ->when($timeframe, fn($query)=>$query->where('timeframe', $timeframe))
                ->when($location, fn($query)=>$query->where('location_id', $location))
                ->when($location_type, fn($query)=>$query->where('location_type_id', $location_type))
                ->when($data_format, fn($query)=>$query->where('data_format_id', $data_format));
        }]);
    }
}
