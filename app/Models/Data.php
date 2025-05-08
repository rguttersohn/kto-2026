<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Data extends Model
{   
    use HasFactory;

    protected $connection = 'supabase';

    protected $table = 'indicators.data';

    protected $fillable = [
        'data',
        'data_format_id',
        'timeframe',
        'location_id',
        'breakdown_id',
        'indicator_id'
    ];

    protected $casts = [
        'data' => 'float'
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
