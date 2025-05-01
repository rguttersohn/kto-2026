<?php

namespace App\Enums;

enum LocationTypeClassification: string
{
    case POLITICAL = 'political';
    case ADMINISTRATIVE = 'administrative';
    case STATISTICAL = 'statistical';
    case GEOGRAPHIC = 'geographic';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::POLITICAL => 'Political',
            self::ADMINISTRATIVE => 'Administrative',
            self::STATISTICAL => 'Statistical',
            self::GEOGRAPHIC => 'Geographic',
            self::OTHER => 'Other',
        };
    }
}