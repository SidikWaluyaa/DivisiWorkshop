<?php

namespace App\Services\Finance;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BankMutationsImport;
use Illuminate\Http\UploadedFile;

class MutationImportService
{
    /**
     * Import bank mutations from Excel/CSV file.
     * Does NOT modify any invoice data.
     *
     * @param UploadedFile $file
     * @return array Import summary stats
     */
    public function importMutation(UploadedFile $file): array
    {
        $import = new BankMutationsImport();
        Excel::import($import, $file);

        return [
            'imported' => $import->getImportedCount(),
            'skipped' => $import->getSkippedCount(),
        ];
    }
}
