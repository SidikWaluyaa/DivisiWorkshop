<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Normalize phone number to "812..." format (stripping 0 and 62 prefixes).
     *
     * @param string|null $phone
     * @return string|null
     */
    public static function normalizeForGreeting(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // 1. Remove non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // 2. Strip prefixes
        if (str_starts_with($clean, '62')) {
            $clean = substr($clean, 2);
        } elseif (str_starts_with($clean, '0')) {
            $clean = substr($clean, 1);
        }

        return $clean;
    }

    /**
     * Standard normalization for WhatsApp links (62812...)
     */
    public static function normalize(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return $clean;
    }
}
