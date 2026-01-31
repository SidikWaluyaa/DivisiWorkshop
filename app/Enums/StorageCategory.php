<?php

namespace App\Enums;

enum StorageCategory: string
{
    case SHOES = 'shoes';
    case ACCESSORIES = 'accessories';
    case BEFORE = 'before'; // Inbound/Transit

    public function label(): string
    {
        return match($this) {
            self::SHOES => 'Rak Sepatu',
            self::ACCESSORIES => 'Rak Aksesoris',
            self::BEFORE => 'Rak Inbound (Transit)',
        };
    }
}
