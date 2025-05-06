<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use App\Events\IndicatorSaved;


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

    protected $dispatchesEvents = [
        'saved' => IndicatorSaved::class
    ];

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
}
