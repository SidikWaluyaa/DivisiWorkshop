<?php

namespace App\Exports;

use App\Models\Invoice;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PiutangAfterExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $search;
    protected $ignoreDate;

    public function __construct($startDate, $endDate, $search = null, $ignoreDate = true)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
        $this->search = $search;
        $this->ignoreDate = $ignoreDate;
    }

    public function array(): array
    {
        $rangeLabel = $this->ignoreDate ? 'Semua Waktu' : ($this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y'));

        // Query data
        $query = Invoice::with(['customer', 'workOrders.workOrderServices.service'])
            ->where('status', '!=', 'Lunas')
            ->where('spk_status', 'SELESAI');

        if (!$this->ignoreDate && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('workOrders', function ($sub) {
                      $sub->where('spk_number', 'like', '%' . $this->search . '%')
                          ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                          ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $invoices = $query->latest()->get();
        $totalOutstanding = $invoices->sum('remaining_balance');
        $totalInvoicesCount = $invoices->count();

        $rows = [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN PIUTANG AFTER'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section 1 Header
            ['RINGKASAN PIUTANG AFTER (SELESAI)'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 7: Metrics
            ['Total Outstanding Piutang', 'Rp ' . number_format($totalOutstanding, 0, ',', '.'), 'Jumlah piutang yang belum tertagih'],
            ['Jumlah Invoice Outstanding', $totalInvoicesCount . ' Invoice', 'Total invoice yang memiliki sisa tagihan'],
            
            // Row 8: Blank separator
            [''],
            
            // Row 9: Section 2 Header
            ['RINCIAN DATA PIUTANG AFTER'],
            // Row 10: Table Headers
            ['No. Invoice', 'No. SPK', 'Nama Pelanggan', 'No. WhatsApp', 'Detail Sepatu', 'Layanan / Jasa', 'Total Biaya', 'Terbayar', 'Sisa Piutang', 'Status']
        ];

        // Row 11+: Items
        if ($invoices->isEmpty()) {
            $rows[] = ['Tidak ada invoice piutang terdata.', '', '', '', '', '', '', '', '', ''];
        } else {
            foreach ($invoices as $invoice) {
                $spkList = $invoice->workOrders->pluck('spk_number')->implode(', ');
                
                $shoeList = $invoice->workOrders->map(function($wo) {
                    return ($wo->shoe_brand ?: '') . ' ' . ($wo->shoe_type ?: '') . ' (Warna: ' . ($wo->shoe_color ?: '-') . ', Size: ' . ($wo->shoe_size ?: '-') . ')';
                })->implode(' | ');

                $servicesList = $invoice->workOrders->flatMap(function($wo) {
                    return $wo->workOrderServices->map(function($svc) {
                        return $svc->custom_service_name ?: ($svc->service->name ?? 'Jasa');
                    });
                })->unique()->implode(', ');

                $rows[] = [
                    $invoice->invoice_number,
                    $spkList,
                    $invoice->customer->name ?? 'N/A',
                    $invoice->customer->phone ?? 'N/A',
                    $shoeList,
                    $servicesList ?: '-',
                    'Rp ' . number_format(($invoice->total_amount + $invoice->shipping_cost - $invoice->discount), 0, ',', '.'),
                    'Rp ' . number_format($invoice->paid_amount, 0, ',', '.'),
                    'Rp ' . number_format($invoice->remaining_balance, 0, ',', '.'),
                    $invoice->status
                ];
            }
        }

        // Blank separator
        $rows[] = [''];
        // Generated meta
        $rows[] = ['Laporan di-generate pada:', date('d/m/Y H:i')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A9:J9');

        $totalRows = $sheet->getHighestRow();

        // Set horizontal alignment
        $sheet->getStyle('B6:B7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G11:I' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A10:J10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e293b']],
            ],
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '475569']],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85'] // Brand Green
                ]
            ],
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569'] // Dark Slate
                ]
            ],
            9 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85']
                ]
            ],
            10 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569']
                ]
            ],
            ($totalRows) => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
