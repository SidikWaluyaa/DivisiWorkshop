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

class OrdersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $failures = [];
        $spkAliases = ['spk', 'no_spk', 'order_id', 'kode_spk', 'spk_number', 'nomor_spk'];
        $custAliases = ['customer', 'nama', 'cust', 'pelanggan', 'nama_customer'];

        // 1. PRE-VALIDATION & ERROR COLLECTION
        $excelSpks = [];
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Assuming heading row is row 1
            
            $getVal = function($aliases) use ($row) {
                foreach ($aliases as $alias) {
                    if (isset($row[$alias])) return $row[$alias];
                }
                return null;
            };

            $rawSpk = $getVal($spkAliases);
            $customer = $getVal($custAliases) ?? 'No Name';

            if (!$rawSpk) {
                // If the entire row is empty, skip it silently or report? 
                // Let's check if there's any data at all in the row.
                if (empty(array_filter($row->toArray()))) continue;
                
                $failures[] = [
                    'spk' => 'N/A',
                    'customer' => $customer,
                    'type' => 'Data Kosong',
                    'message' => "Baris ke-{$rowNumber}: Nomor SPK tidak ditemukan."
                ];
                continue;
            }

            $spk = trim((string)$rawSpk);

            // Check for Internal Duplicates in Excel
            if (in_array($spk, $excelSpks)) {
                $failures[] = [
                    'spk' => $spk,
                    'customer' => $customer,
                    'type' => 'Duplikat di Excel',
                    'message' => "Baris ke-{$rowNumber}: Nomor SPK '{$spk}' muncul lebih dari satu kali dalam file ini."
                ];
            }
            $excelSpks[] = $spk;

            // Check for DB Duplicates (including Trashed)
            if (WorkOrder::withTrashed()->where('spk_number', $spk)->exists()) {
                $failures[] = [
                    'spk' => $spk,
                    'customer' => $customer,
                    'type' => 'Duplikat Data',
                    'message' => "Nomor SPK '{$spk}' sudah terdaftar di sistem (atau berada di Tempat Sampah)."
                ];
            }

            // Validate Dates
            $testDates = [
                'Tanggal Masuk' => $getVal(['tanggal_masuk', 'entry_date', 'date_in']),
                'Estimasi' => $getVal(['estimasi_selesai', 'est_date', 'due_date'])
            ];

            foreach ($testDates as $label => $val) {
                if ($val && !is_numeric($val)) {
                    try {
                        \Carbon\Carbon::parse($val);
                    } catch (\Exception $e) {
                        $failures[] = [
                            'spk' => $spk,
                            'customer' => $customer,
                            'type' => 'Format Tanggal',
                            'message' => "{$label} memiliki format tidak valid ('{$val}'). Gunakan format YYYY-MM-DD atau format tanggal Excel."
                        ];
                    }
                }
            }
        }

        // If there are any failures, stop everything and report
        if (!empty($failures)) {
            throw new \App\Exceptions\ImportValidationException($failures);
        }

        // 2. PROCESS INSERT (Only if no failures)
        foreach ($rows as $row) {
            $getVal = function($aliases) use ($row) {
                foreach ($aliases as $alias) {
                    if (isset($row[$alias])) return $row[$alias];
                }
                return null;
            };

            $rawSpk = $getVal($spkAliases);
            if (!$rawSpk && empty(array_filter($row->toArray()))) continue;

            $spk = trim((string)$rawSpk);
            
            // Date Parser
            $parseDate = function($val) {
                if (!$val) return now();
                try {
                    if (is_numeric($val)) return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                    return \Carbon\Carbon::parse($val);
                } catch (\Throwable $e) { return now(); }
            };

            WorkOrder::create([
                'spk_number'    => $spk,
                'customer_name' => $getVal($custAliases) ?? 'No Name', 
                'customer_phone'=> trim((string)$getVal(['no_wa', 'wa', 'phone', 'hp', 'no_hp', 'telephone'])) ?: '-', 
                'customer_email'=> filter_var($row['email'] ?? null, FILTER_VALIDATE_EMAIL) ? $row['email'] : null,
                'customer_address'=> $row['alamat'] ?? '-',
                'shoe_brand'    => $getVal(['brand', 'merk', 'merek', 'sepatu']) ?? 'Unknown',
                'shoe_size'     => $getVal(['size', 'ukuran']) ?? '-',
                'shoe_color'    => $getVal(['warna', 'color']) ?? '-',
                'status'        => WorkOrderStatus::DITERIMA->value,
                'current_location' => 'Gudang Penerimaan', 
                'category'      => $row['jenis'] ?? null,
                'entry_date'    => $parseDate($getVal(['tanggal_masuk', 'entry_date', 'date_in'])),
                'estimation_date' => $parseDate($getVal(['estimasi_selesai', 'est_date', 'due_date'])),
                'priority'      => $getVal(['prioritas', 'priority']) ?? 'Reguler',
                'notes'         => $getVal(['catatan', 'notes', 'keterangan']),
                'created_by'    => Auth::id(),
            ]);
        }
    }
}
