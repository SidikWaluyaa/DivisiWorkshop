<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\WorkshopSummarySheet;
use App\Exports\Sheets\WorkshopOrdersSheet;

class WorkshopReportExport implements WithMultipleSheets
{
    protected $data;
    protected $orders;

    public function __construct($data, $orders)
    {
        $this->data = $data;
        $this->orders = $orders;
    }

    public function sheets(): array
    {
        return [
            new WorkshopSummarySheet($this->data),
            new WorkshopOrdersSheet($this->orders),
        ];
    }
}
