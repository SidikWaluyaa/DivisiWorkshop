<?php

namespace App\Imports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ServicesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['name'])) {
            return null;
        }

        return Service::updateOrCreate(
            [
                'name' => $row['name'],
                'category' => $row['category'] ?? 'General',
            ],
            [
                'price' => $row['price'] ?? 0,
                'duration_minutes' => $row['duration_minutes'] ?? 60,
                'description' => $row['description'] ?? null,
            ]
        );
    }
}
