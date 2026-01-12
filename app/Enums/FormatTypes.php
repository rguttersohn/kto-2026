<?php

namespace App\Enums;

enum FormatTypes : string
{
    case GEOJSON = 'geojson';
    case CSV = 'csv';
    case JSON = 'json';
}
