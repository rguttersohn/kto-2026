<?php

namespace App\Enums;

enum GeometryTypes: string
{
    case ST_Point = 'Point';
    case ST_Polygon = 'Polygon';
    case ST_MultiPolygon = 'MultiPolygon';
   

    public function label(): string
    {
        return match($this) {
            self::ST_Point => 'ST_Point',
            self::ST_Polygon => 'ST_Polygon',
            self::ST_MultiPolygon => 'ST_MultiPolygon',
        };
    }
}
