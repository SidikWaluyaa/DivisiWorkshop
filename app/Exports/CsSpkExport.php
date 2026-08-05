<?php

namespace App\Exports;

use App\Models\CsSpk;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class CsSpkExport implements FromQuery, WithHeadings, WithMapping, WithCustomStartCell, WithEvents, ShouldAutoSize
{
    protected $filters;
    protected $totalSpk;
    protected $totalRevenue;
    protected $rowNum = 0;

    public function __construct($filters = [], $totalSpk = 0, $totalRevenue = 0)
    {
        $this->filters = $filters;
        $this->totalSpk = $totalSpk;
        $this->totalRevenue = $totalRevenue;
    }

    public function query()
    {
        $query = CsSpk::with(['lead', 'customer', 'items.workOrder'])
            ->orderBy('created_at', 'desc');

        if (isset($this->filters['date_from']) && $this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to']) && $this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function headings(): array
    {
        return [
            'No',
            'No SPK',
            'Tanggal',
            'Nama Customer',
            'No Telepon',
            'Detail Item & Jasa',
            'Total Transaksi',
            'DP Amount',
            'Status SPK'
        ];
    }

    public function map($spk): array
    {
        $this->rowNum++;
        
        $itemsDetails = [];
        if ($spk->items && count($spk->items) > 0) {
            foreach ($spk->items as $item) {
                $services = '';
                if (is_array($item->services)) {
                    $services = collect($item->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ');
                } else {
                    $services = $item->services;
                }
                $itemsDetails[] = $item->shoe_brand . " (" . $item->shoe_type . ") - " . $services;
            }
        } elseif ($spk->shoe_brand) {
            $services = '';
            if (is_array($spk->services)) {
                $services = collect($spk->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ');
            } else {
                $services = $spk->services;
            }
            $itemsDetails[] = $spk->shoe_brand . " (" . $spk->shoe_type . ") - " . $services;
        }
        $detailJasa = implode("\n", $itemsDetails);

        return [
            $this->rowNum,
            $spk->spk_number,
            $spk->created_at->format('d M Y H:i'),
            $spk->lead?->customer_name ?? 'Unknown Customer',
            $spk->lead?->customer_phone ?? '-',
            $detailJasa,
            (float) $spk->total_price,
            (float) $spk->dp_amount,
            $spk->label
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set sheet title and styling
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI SPK CS');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '22AF85']
                    ]
                ]);
                $sheet->getRowDimension(1)->setRowHeight(40);

                // Build filter meta text
                $dateFrom = $this->filters['date_from'] ?? null;
                $dateTo = $this->filters['date_to'] ?? null;
                $status = $this->filters['status'] ?? null;
                
                $periodeText = 'Semua Periode Tanggal';
                if ($dateFrom && $dateTo) {
                    $periodeText = \Carbon\Carbon::parse($dateFrom)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($dateTo)->format('d M Y');
                } elseif ($dateFrom) {
                    $periodeText = 'Mulai ' . \Carbon\Carbon::parse($dateFrom)->format('d M Y');
                } elseif ($dateTo) {
                    $periodeText = 'Sampai ' . \Carbon\Carbon::parse($dateTo)->format('d M Y');
                }
                
                $statusText = $status ? str_replace('_', ' ', $status) : 'Semua Status';
                $metaText = "Periode: {$periodeText} | Status: {$statusText} | Dicetak: " . now()->format('d M Y H:i');

                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', $metaText);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'color' => ['rgb' => '64748B']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ]
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Summary Metrics Row 4 & 5
                $sheet->mergeCells('A4:C4');
                $sheet->setCellValue('A4', 'TOTAL SPK DIBUAT:');
                $sheet->getStyle('A4')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748b'));
                $sheet->mergeCells('D4:I4');
                $sheet->setCellValue('D4', $this->totalSpk . ' SPK');
                $sheet->getStyle('D4')->getFont()->setBold(true);

                $sheet->mergeCells('A5:C5');
                $sheet->setCellValue('A5', 'TOTAL NILAI TRANSAKSI (OMZET):');
                $sheet->getStyle('A5')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748b'));
                $sheet->mergeCells('D5:I5');
                $sheet->setCellValue('D5', 'Rp ' . number_format($this->totalRevenue, 0, ',', '.'));
                $sheet->getStyle('D5')->getFont()->setBold(true);

                $sheet->getStyle('A4:I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                $sheet->getStyle('A4:I5')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
                $sheet->getRowDimension(4)->setRowHeight(25);
                $sheet->getRowDimension(5)->setRowHeight(25);

                // Table Headings Styling (Row 7)
                $sheet->getStyle('A7:I7')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '475569']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0']
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '94A3B8']
                        ]
                    ]
                ]);
                $sheet->getRowDimension(7)->setRowHeight(25);

                // Align header fields
                $sheet->getStyle('A7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('I7')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Format data rows
                $highestRow = $sheet->getHighestRow();
                for ($row = 8; $row <= $highestRow; $row++) {
                    // Set thin borders
                    $sheet->getStyle("A{$row}:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

                    // Zebra striping
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
                    }

                    // Enable text wrapping on Detail Jasa
                    $sheet->getStyle("F{$row}")->getAlignment()->setWrapText(true);

                    // Alignments
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("B{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("D{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("E{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("F{$row}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("H{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                    $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

                    // Currency Formats
                    $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
                    $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

                    // Conditional Styling on Status SPK (Column I)
                    $statusValue = $sheet->getCell("I{$row}")->getValue();
                    $bgColor = 'F1F5F9';
                    $textColor = '475569';

                    if (strpos($statusValue, 'DP Lunas') !== false) {
                        $bgColor = 'DCFCE7';
                        $textColor = '15803D';
                    } elseif (strpos($statusValue, 'Menunggu DP') !== false) {
                        $bgColor = 'FEF9C3';
                        $textColor = 'A16207';
                    } elseif (strpos($statusValue, 'Workshop') !== false || strpos($statusValue, 'Verifikasi') !== false) {
                        $bgColor = 'E0F2FE';
                        $textColor = '0369A1';
                    }

                    $sheet->getStyle("I{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => $textColor]
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor]
                        ]
                    ]);
                }
            }
        ];
    }
}
