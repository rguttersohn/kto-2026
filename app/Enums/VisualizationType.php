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
}
