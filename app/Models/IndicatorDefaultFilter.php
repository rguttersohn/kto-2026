<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    /**
     * 
     * relationships
     * 
     * 
     */

    public function breakdownFilter(){

       return $this->belongsTo(Breakdown::class, 'breakdown_id');

    }

    public function formatFilter(){

        return $this->belongsTo(DataFormat::class, 'data_format_id');
    }

    public function locationTypeFilter(){

        return $this->belongsTo(LocationType::class, 'location_type_id');
    
    }

    public function locationFilter(){

        return $this->belongsTo(Location::class, 'location_id');

    }

    /**
     * 
     * attribute casting column so they match filter names
     * 
     */
    protected $appends = ['format', 'breakdown', 'location', 'location_type'];

    protected function format():Attribute{

        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['data_format_id'],
        );

    }

    protected function breakdown():Attribute{

        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['breakdown_id'],
        );

    }

    protected function locationType():Attribute{

        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['location_type_id'],
        );

    }

    protected function location():Attribute{

        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['location_id'],
        );

    }

}
