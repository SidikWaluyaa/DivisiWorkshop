<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Normalize phone number to remove leading 0, 62, or +62.
     * Result will start dangan the operator code (e.g. 812...).
     * 
     * @param string|null $phone
     * @return string|null
     */
    public static function normalize($phone)
    {
        if (!$phone) {
            return null;
        }

        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($phone === '') {
            return null;
        }

        // Remove leading +62 or 62
        if (str_starts_with($phone, '62')) {
            $phone = substr($phone, 2);
        }

        // Remove leading 0
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return $phone;
    }
}
