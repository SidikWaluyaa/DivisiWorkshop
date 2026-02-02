<?php

namespace App\Enums;

enum StorageCategory: string
{
    case SHOES = 'shoes';
    case ACCESSORIES = 'accessories';
    case BEFORE = 'before'; // Inbound/Transit
    case MANUAL = 'manual'; // Gudang Manual (General)
    case MANUAL_TL = 'manual_tl'; // Tagih Lunas
    case MANUAL_TN = 'manual_tn'; // Tagih Nanti
    case MANUAL_L = 'manual_l'; // Lunas

    public function label(): string
    {
        return match($this) {
            self::SHOES => 'Rak Sepatu',
            self::ACCESSORIES => 'Rak Aksesoris',
            self::BEFORE => 'Rak Inbound (Transit)',
            self::MANUAL => 'Rak Manual (Umum)',
            self::MANUAL_TL => 'Rak Manual (Tagih Lunas)',
            self::MANUAL_TN => 'Rak Manual (Tagih Nanti)',
            self::MANUAL_L => 'Rak Manual (Lunas)',
        };
    }
}
