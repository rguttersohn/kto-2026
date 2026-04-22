<?php

namespace App\Enums;

enum VisualizationType: string
{
    case Bar = 'bar';
    case Line = 'line';
    case Pie = 'pie';
    case Card = 'card';

    public function label(): string
    {
        return match($this) {
            self::Bar => 'Bar',
            self::Line => 'Line',
            self::Pie => 'Pie',
            self::Card => 'Card',
        };
    }

    /**
     * 
     * Returns an array of filters that should be excluded from the default filters for the indicator based on the visualization type. 
     * This is used to ensure that the default filters do not include filters that are not relevant to the visualization type. 
     * For example, a line chart may not have a breakdown filter, so it should be excluded from the default filters for indicators with a line chart visualization type.
     *
    */
    public function excludedDefaultFilters(): array
    {
        return match($this) {
            self::Line => ['timeframe'],
            self::Bar  => ['breakdown'],
            self::Pie  => ['breakdown'],
            self::Card => [],
            default => ['timeframe']
        };
    }
}
