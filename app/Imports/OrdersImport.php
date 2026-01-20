<?php

namespace App\Imports;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Exceptions\ImportValidationException;
use Maatwebsite\Excel\Concerns\WithStartRow;

class OrdersImport implements ToCollection, WithHeadingRow, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Header is row 1, Example is row 2. Maatwebsite considers 'headingRow' logic separately.
        // Actually, WithHeadingRow defaults to 1. If we have example data on row 2, we want to skip it OR treat row 1 as header.
        // If row 2 is example data and we want actual data to start from 3.
        // But WithHeadingRow uses row 1 as keys. 
        // If we want to ignore row 2 (example), we can check in the loop or use a filter.
        // Let's implement logic to skip the example row inside collection() if it matches strict example values, or use startRow if we move header.
        // Better strategy: Keep Header at 1. Row 2 is example. Start real import from 3?
        // Maatwebsite 'WithHeadingRow' parses row 1. Then 'collection' receives rows starting from 2.
        // So row 2 (example) will be in the collection. We should filter it out.
    }

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $failures = [];
        $rowsToCreate = [];
        $validatedRows = [];

        // Safe date parsing helper
        $parseDate = function($val) {
            if (!$val) return now();
            
            try {
                // If it's a numeric Excel serialized date
                if (is_numeric($val)) {
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                }
                // If it's a string, try standard parsing
                return \Carbon\Carbon::parse($val);
            } catch (\Throwable $e) { 
                return now(); 
            }
        };

        // Helper to check if row is the example row
        $isExampleRow = fn($row) => ($row['spk'] ?? '') === 'CONTOH-001' && ($row['customer'] ?? '') === 'Budi Santoso';

        $spkMap = []; 
        $allSpksInFile = [];
        
        foreach ($rows as $index => $row) {
            // Skip the example row explicitly
            if ($isExampleRow($row)) continue;

            // Normalize Keys: Template uses 'SPK', 'Customer', 'No WA', etc. 
            // Maatwebsite slugifies them: 'spk', 'customer', 'no_wa', 'email', 'alamat', 'brand', 'size', 'warna', 'tanggal_masuk', 'estimasi_selesai', 'prioritas'
            
            $excelSpk = $row['spk'] ?? null;
            
            if ($excelSpk) {
                if (isset($allSpksInFile[$excelSpk])) {
                    $failures[] = [
                        'spk' => $excelSpk,
                        'customer' => $row['customer'] ?? 'No Name',
                        'type' => 'Duplikat dalam File',
                        'message' => "SPK {$excelSpk} duplikat di dalam file."
                    ];
                } else {
                    $allSpksInFile[$excelSpk] = true;
                    $spkMap[$excelSpk] = $row;
                }
            } else {
                // Auto-generate SPK if empty
                $row['__generated_spk'] = 'SPK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }

            // Date Validation
            $rawEntry = $row['tanggal_masuk'] ?? null;
            $rawEst = $row['estimasi_selesai'] ?? null;

            if ($rawEntry && $rawEst) {
                try {
                    $entryDate = $parseDate($rawEntry);
                    $estDate = $parseDate($rawEst); // Use safe parser

                    // Strip time for accurate date comparison
                    $entryDate->setTime(0, 0);
                    $estDate->setTime(0, 0);

                    if ($estDate < $entryDate) {
                        $failures[] = [
                            'spk' => $excelSpk ?? 'New Order',
                            'customer' => $row['customer'] ?? 'No Name',
                            'type' => 'Tanggal Invalid',
                            'message' => "Estimasi selesai tidak boleh lebih lampau dari tanggal masuk."
                        ];
                    }
                } catch (\Exception $e) { }
            }

            $validatedRows[] = $row;
        }

        // DB Duplicate Check
        if (!empty($spkMap)) {
            $existingSpks = WorkOrder::whereIn('spk_number', array_keys($spkMap))->pluck('spk_number')->toArray();
            foreach ($existingSpks as $existing) {
                $row = $spkMap[$existing];
                $failures[] = [
                    'spk' => $existing,
                    'customer' => $row['customer'] ?? 'No Name',
                    'type' => 'Duplikat Database',
                    'message' => "SPK {$existing} sudah ada di sistem."
                ];
            }
        }

        if (!empty($failures)) {
            throw new ImportValidationException($failures);
        }

        // Processing Valid Rows
        foreach ($validatedRows as $row) {
            $spk = $row['spk'] ?? ($row['__generated_spk'] ?? 'SPK-' . date('Ymd') . '-' . strtoupper(Str::random(4)));
            $email = filter_var($row['email'] ?? null, FILTER_VALIDATE_EMAIL) ? $row['email'] : null;

            WorkOrder::create([
                'spk_number'    => $spk,
                'customer_name' => $row['customer'] ?? 'No Name', 
                'customer_phone'=> $row['no_wa'] ?? '-', 
                'customer_email'=> $email,
                'customer_address'=> $row['alamat'] ?? null,
                'shoe_brand'    => $row['brand'] ?? 'Unknown',
                'shoe_size'     => $row['size'] ?? '-',
                'shoe_color'    => $row['warna'] ?? '-',
                'status'        => WorkOrderStatus::DITERIMA->value,
                'current_location' => 'Gudang Penerimaan', 
                'entry_date'    => $parseDate($row['tanggal_masuk'] ?? null),
                'estimation_date' => $parseDate($row['estimasi_selesai'] ?? null),
                'priority'      => $row['prioritas'] ?? 'Reguler',
                'created_by'    => Auth::id(),
            ]);
        }
    }
}
