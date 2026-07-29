<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CxUpsellExport implements WithMultipleSheets
{
    protected $upsell;
    protected $startDate;
    protected $endDate;

    public function __construct($upsell, $startDate, $endDate)
    {
        $this->upsell = $upsell;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new \App\Exports\Sheets\CxTambahJasaSheet($this->upsell, $this->startDate, $this->endDate),
            new \App\Exports\Sheets\CxOtoSheet($this->upsell, $this->startDate, $this->endDate),
        ];
    }
}
