<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;


enum IndicatorFilterTypes:string {
    
    case LOCATION_TYPE = 'location_type';
    case TIMEFRAME = 'timeframe';
    case BREAKDOWN = 'breakdown';
    case LOCATION = 'location';
    case FORMAT = 'format';
    
    public function label(): string
    {
        return match($this) {
            self::LOCATION_TYPE => 'Location Type',
            self::TIMEFRAME => 'Timeframe',
            self::BREAKDOWN => 'Breakdown',
            self::LOCATION => 'Location',
            self::FORMAT => 'Format'
        };
    }
}
