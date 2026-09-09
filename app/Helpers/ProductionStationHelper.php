<?php

namespace App\Helpers;

class ProductionStationHelper
{
    /**
     * Categorize a service/category name into standard workshop production stations.
     *
     * Stations:
     * 1. SOLING (Order: 1) -> Soling, Ganti Sol, Reparasi Sol, Bongkar Sol
     * 2. UPPER (Order: 2)  -> Upper, Reparasi Upper, Jahit Upper, Patch Upper
     * 3. TREATMENT (Order: 3) -> Cleaning, Treatment, Repaint, Whitening, Aksesoris, Tambahan, Custom
     *
     * @param string|null $categoryName
     * @return array{code: string, name: string, order: int}
     */
    public static function getStationInfo(?string $categoryName): array
    {
        if (empty($categoryName)) {
            return [
                'code'  => 'TREATMENT',
                'name'  => 'Stasiun Repaint & Treatment',
                'order' => 3,
            ];
        }

        $cat = strtolower(trim($categoryName));

        // Stasiun 1: Soling / Bongkar Sol
        $solKeywords = ['sol', 'sole', 'midsole', 'outsole', 'reglue', 'lem', 'heel', 'hak', 'tapak', 'welt', 'tpr', 'vibram', 'stuck on', 'sponge', 'lapis'];
        if (\Illuminate\Support\Str::contains($cat, $solKeywords)) {
            return [
                'code'  => 'SOLING',
                'name'  => 'Stasiun Soling',
                'order' => 1,
            ];
        }

        // Stasiun 2: Upper / Jahit / Zipper
        $upperKeywords = ['upper', 'lining', 'insole', 'patch', 'jahit', 'zipper', 'resleting', 'sleting', 'strap', 'buckle', 'gesper', 'elastis', 'counter', 'tongue', 'lidah', 'eyelet', 'tali', 'velcro', 'karet', 'pad'];
        if (\Illuminate\Support\Str::contains($cat, $upperKeywords)) {
            return [
                'code'  => 'UPPER',
                'name'  => 'Stasiun Upper',
                'order' => 2,
            ];
        }

        // Stasiun 3: Repaint, Cleaning, Treatment, Whitening, Aksesoris, Tambahan, Custom, dll.
        return [
            'code'  => 'TREATMENT',
            'name'  => 'Stasiun Repaint & Treatment',
            'order' => 3,
        ];
    }

    /**
     * Get numeric station order (1, 2, or 3) for sorting services
     */
    public static function getStationOrder(?string $categoryName): int
    {
        return self::getStationInfo($categoryName)['order'];
    }

    /**
     * Get station code ('SOLING', 'UPPER', 'TREATMENT')
     */
    public static function getStationCode(?string $categoryName): string
    {
        return self::getStationInfo($categoryName)['code'];
    }
}
