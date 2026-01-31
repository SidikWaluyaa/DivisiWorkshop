<?php

namespace App\Enums;

enum RackStatus: string
{
    case ACTIVE = 'active';
    case MAINTENANCE = 'maintenance';
    case FULL = 'full';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::MAINTENANCE => 'Maintenance',
            self::FULL => 'Full',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::MAINTENANCE => 'yellow',
            self::FULL => 'red',
        };
    }
}
