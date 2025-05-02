<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datum extends Model
{
    
    protected $connection = 'supabase';

    protected $table = 'indicators.data';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'indicator_id',
        'year',
        'value',
        'source',
        'source_url',
        'data_format_id',
    ];

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id', 'id');
    }

    public function dataFormat()
    {
        return $this->belongsTo(DataFormat::class, 'data_format_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
    
    public function breakdown()
    {
        return $this->belongsTo(Breakdown::class, 'breakdown_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
