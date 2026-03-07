<?php

namespace App\Imports;

use App\Models\BankMutation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class BankMutationsImport implements ToCollection, WithHeadingRow
{
    protected int $importedCount = 0;
    protected int $skippedCount = 0;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Column aliases for flexible Excel headers
        $dateAliases = ['transaction_date', 'tanggal', 'tgl', 'date', 'tanggal_transaksi'];
        $invoiceAliases = ['invoice_number', 'no_invoice', 'invoice', 'nomor_invoice', 'no_inv'];
        $amountAliases = ['amount', 'nominal', 'jumlah', 'nilai'];
        $descAliases = ['description', 'keterangan', 'deskripsi', 'berita', 'catatan'];
        $bankAliases = ['bank_code', 'bank', 'kode_bank', 'nama_bank'];
        $typeAliases = ['mutation_type', 'type', 'tipe', 'jenis', 'db_cr', 'cr_db'];

        foreach ($rows as $row) {
            // Skip completely empty rows
            if (empty(array_filter($row->toArray()))) {
                continue;
            }

            $getVal = function ($aliases) use ($row) {
                foreach ($aliases as $alias) {
                    if (isset($row[$alias]) && $row[$alias] !== null && $row[$alias] !== '') {
                        return $row[$alias];
                    }
                }
                return null;
            };

            $rawDate = $getVal($dateAliases);
            $rawAmount = $getVal($amountAliases);

            // Skip rows without essential data
            if (!$rawDate || !$rawAmount) {
                $this->skippedCount++;
                continue;
            }

            // Parse date
            try {
                if (is_numeric($rawDate)) {
                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate);
                } else {
                    $date = Carbon::parse($rawDate);
                }
            } catch (\Throwable $e) {
                $this->skippedCount++;
                continue;
            }

            // Parse amount (handle formatted numbers like "1,500,000" or "1.500.000")
            $amount = $rawAmount;
            if (is_string($amount)) {
                $amount = str_replace(['.', ','], ['', '.'], $amount);
                $amount = (float) preg_replace('/[^0-9.]/', '', $amount);
            }

            // Parse mutation type
            $rawType = strtoupper(trim((string) ($getVal($typeAliases) ?? 'CR')));
            $mutationType = in_array($rawType, ['CR', 'DB']) ? $rawType : 'CR';

            BankMutation::create([
                'transaction_date' => $date,
                'invoice_number' => trim((string) ($getVal($invoiceAliases) ?? '')),
                'amount' => abs($amount),
                'description' => $getVal($descAliases),
                'bank_code' => strtoupper(trim((string) ($getVal($bankAliases) ?? ''))),
                'mutation_type' => $mutationType,
                'used' => false,
            ]);

            $this->importedCount++;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
