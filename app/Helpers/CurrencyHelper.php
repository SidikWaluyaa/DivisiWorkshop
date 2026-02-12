<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format number to Rupiah (Rp 175.000)
     *
     * @param float|int|string|null $amount
     * @param bool $withPrefix
     * @return string
     */
    public static function formatRupiah($amount, $withPrefix = true)
    {
        if ($amount === null || $amount === '') {
            return $withPrefix ? 'Rp 0' : '0';
        }

        // Ensure it's a number
        $amount = (float) $amount;

        $formatted = number_format($amount, 0, ',', '.');

        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}
