<?php

namespace App\Enums;

enum LocationScopes: string
{
    case LOCAL = 'local';
    case STATE = 'state';

    public function label(): string
    {
        return match ($this) {
            self::LOCAL => 'local',
            self::STATE => 'state',
        };
    }
}
