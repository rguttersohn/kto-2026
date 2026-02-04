<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicatorDefaultFilter extends Model
{   

    protected $table = 'indicators.default_filters';


    protected $fillable = [
        'indicator_id', 
        'timeframe', 
        'data_format_id',
        'breakdown_id',
        'location_type_id',
        'location_id'
    ];

    public function breakdown(){

       return $this->belongsTo(Breakdown::class);

    }

    public function dataFormat(){

        return $this->belongsTo(DataFormat::class);
    }

    public function locationType(){

        return $this->belongsTo(LocationType::class);
    
    }

    public function location(){

        return $this->belongsTo(Location::class);

    }


}
