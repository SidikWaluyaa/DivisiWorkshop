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

class OrdersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $failures = []; // Structured errors: ['spk' => '', 'type' => '', 'message' => '']
        $rowsToCreate = [];

        // Flexible key check helper
        $get = fn($row, $keys, $default = null) => collect((array)$keys)->map(fn($k) => $row[$k] ?? null)->filter()->first() ?? $default;

        $spkMap = []; 
        $allSpksInFile = []; // Track all SPKs to detect within-file duplicates
        
        foreach ($rows as $index => $row) {
            $excelSpk = $get($row, ['spk', 'no_spk', 'spk_number', 'nomor_spk', 'no_order', 'order_id', 'id_transaksi']);
            
            if ($excelSpk) {
                // Check for duplicates within the Excel file itself
                if (isset($allSpksInFile[$excelSpk])) {
                    $customerName = $get($row, ['nama', 'nama_customer', 'customer', 'customer_name'], 'No Name');
                    $failures[] = [
                        'spk' => $excelSpk,
                        'customer' => $customerName,
                        'type' => 'Duplikat dalam File',
                        'message' => "SPK {$excelSpk} muncul lebih dari 1x dalam file Excel ini. Hapus baris duplikat."
                    ];
                } else {
                    $allSpksInFile[$excelSpk] = true;
                    $spkMap[$excelSpk] = $row;
                }
            } else {
                $row['__generated_spk'] = 'SPK-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                $rowsToCreate[] = $row;
            }

            // Date Validation
            $rawEntry = $get($row, ['tanggal_masuk', 'tanggal_masuk_workshop', 'entry_date']);
            $rawEst = $get($row, ['estimasi_selesai', 'estimasi', 'estimation']);

            if ($rawEntry && $rawEst) {
                try {
                    $entryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawEntry);
                    $estDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawEst);
                    
                    $entryDate->setTime(0, 0);
                    $estDate->setTime(0, 0);

                    if ($estDate < $entryDate) {
                        $displaySpk = $excelSpk ?? 'New Order';
                        $customerName = $get($row, ['nama', 'nama_customer', 'customer', 'customer_name'], 'No Name');
                        
                        $failures[] = [
                            'spk' => $displaySpk,
                            'customer' => $customerName,
                            'type' => 'Tanggal Invalid',
                            'message' => "Estimasi ({$estDate->format('d/m/Y')}) lebih lampau dari Tanggal Masuk ({$entryDate->format('d/m/Y')})"
                        ];
                    }
                } catch (\Exception $e) { }
            }
        }

        // Check for Duplicates in DB
        if (!empty($spkMap)) {
            $existingSpks = WorkOrder::whereIn('spk_number', array_keys($spkMap))->get(); 
            
            if ($existingSpks->isNotEmpty()) {
                foreach ($existingSpks as $existingOrder) {
                    // Use Excel Data for Customer Name to help user identify the row in their file
                    $row = $spkMap[$existingOrder->spk_number] ?? [];
                    $customerName = $get($row, ['nama', 'nama_customer', 'customer', 'customer_name'], 'No Name');

                    $failures[] = [
                        'spk' => $existingOrder->spk_number,
                        'customer' => $customerName,
                        'type' => 'Duplikat Data',
                        'message' => "SPK {$existingOrder->spk_number} sudah terdaftar di sistem. Hapus baris ini dari Excel atau ubah nomor SPK-nya."
                    ];
                }
            }
        }

        if (!empty($failures)) {
            throw new ImportValidationException($failures);
        }

        // Insert Valid Data
        foreach ($rows as $row) {
            $excelSpk = $get($row, ['spk', 'no_spk', 'spk_number', 'nomor_spk', 'no_order', 'order_id', 'id_transaksi']);
            $spk = $excelSpk ?? ($row['__generated_spk'] ?? 'SPK-' . date('Ymd') . '-' . strtoupper(Str::random(4)));

            // Get and validate email (optional)
            $email = $get($row, ['email', 'customer_email', 'email_customer', 'e_mail']);
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null; // Invalid email format, set to null
            }

            WorkOrder::create([
                'spk_number'    => $spk,
                'customer_name' => $get($row, ['nama', 'nama_customer', 'customer', 'customer_name'], 'No Name'), 
                'customer_phone'=> $get($row, ['nomor_wa', 'no_wa', 'telepon', 'phone', 'hp', 'whatsapp']) ?? '-', 
                'customer_email'=> $email,
                'customer_address'=> $get($row, ['alamat', 'address', 'lokasi']) ?? null,
                
                'shoe_brand'    => $get($row, ['brand', 'merk', 'sepatu', 'shoe_brand']) ?? 'Unknown',
                'shoe_size'     => $get($row, ['size', 'ukuran', 'shoe_size']) ?? '-',
                'shoe_color'    => $get($row, ['warna', 'color', 'colour', 'shoe_color']) ?? '-',
                
                'status'        => WorkOrderStatus::DITERIMA->value,
                'current_location' => 'Gudang Penerimaan', 
                'entry_date'    => ($val = $get($row, ['tanggal_masuk', 'tanggal_masuk_workshop', 'entry_date'])) 
                                    ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)) 
                                    : now(),
                'estimation_date' => ($val = $get($row, ['estimasi_selesai', 'estimasi', 'estimation'])) 
                                    ? (\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)) 
                                    : now()->addDays(3),
                'priority'      => $get($row, ['prioritas', 'priority']) ?? 'Normal',
                'created_by'    => Auth::id(),
            ]);
        }
    }
}
