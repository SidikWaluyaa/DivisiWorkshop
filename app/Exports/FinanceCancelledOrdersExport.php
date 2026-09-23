<?php

namespace App\Exports;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FinanceCancelledOrdersExport implements FromArray, WithColumnWidths, WithStyles
{
    protected $search;
    protected $dateFrom;
    protected $dateTo;

    // Track rows for styling & conditional formatting
    protected $refundRows = [];
    protected $paidRows = [];
    protected $summaryRowIndex = 0;
    protected $dataStartRow = 14;
    protected $dataEndRow = 14;

    public function __construct($search = null, $dateFrom = null, $dateTo = null)
    {
        $this->search = $search;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No.
            'B' => 20,  // No. SPK
            'C' => 20,  // Tanggal Batal
            'D' => 26,  // Nama Pelanggan
            'E' => 18,  // No. WhatsApp
            'F' => 32,  // Detail Sepatu
            'G' => 22,  // Estimasi Kerugian
            'H' => 22,  // Uang Masuk Customer
            'I' => 22,  // Nominal Refund
            'J' => 22,  // Sisa Kas Tertahan
            'K' => 35,  // Alasan Pembatalan
            'L' => 30,  // Catatan Tambahan Refund
        ];
    }

    public function array(): array
    {
        // 1. Build Query matching finance cancelled logic
        $query = WorkOrder::where('status', WorkOrderStatus::BATAL)
            ->with(['payments', 'invoice', 'refundBy', 'customer']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'LIKE', "%{$this->search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$this->search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$this->search}%")
                  ->orWhere('reception_rejection_reason', 'LIKE', "%{$this->search}%")
                  ->orWhere('refund_notes', 'LIKE', "%{$this->search}%");
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('updated_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('updated_at', '<=', $this->dateTo);
        }

        $orders = $query->orderBy('updated_at', 'desc')->get();

        // 2. Metrics calculation
        $totalLost = (float) $orders->sum('total_transaksi');
        $totalOrdersCount = $orders->count();
        $totalRefund = (float) $orders->sum('refund_amount');
        
        $totalPaid = 0.0;
        $totalSisaKas = 0.0;
        foreach ($orders as $order) {
            $paid = (float) ($order->invoice?->paid_amount ?? $order->payments->sum('amount_total'));
            $refund = (float) ($order->refund_amount ?? 0);
            $totalPaid += $paid;
            $totalSisaKas += max(0, $paid - $refund);
        }

        $periodLabel = 'Semua Tanggal';
        if ($this->dateFrom && $this->dateTo) {
            $periodLabel = Carbon::parse($this->dateFrom)->format('d/m/Y') . ' s/d ' . Carbon::parse($this->dateTo)->format('d/m/Y');
        } elseif ($this->dateFrom) {
            $periodLabel = 'Dari ' . Carbon::parse($this->dateFrom)->format('d/m/Y');
        } elseif ($this->dateTo) {
            $periodLabel = 'Sampai ' . Carbon::parse($this->dateTo)->format('d/m/Y');
        }

        // 3. Assemble Header & Summary Table
        // Note: For merged cells, the value is placed in the top-left cell of the merge range.
        $rows = [
            // Row 1: Title (Merged A1:L1)
            ['SHOEWORKSHOP — LAPORAN TRANSAKSI BATAL & DANA REFUND'],
            // Row 2: Subtitle / Meta (Merged A2:L2)
            ['Periode: ' . $periodLabel . '   |   Kata Kunci Filter: ' . ($this->search ?: 'Semua') . '   |   Tanggal Unduh: ' . date('d/m/Y H:i') . ' WIB'],
            // Row 3: Blank
            [''],
            // Row 4: Summary Section Header (Merged A4:H4)
            ['RINGKASAN METRIK KEUANGAN & OPERASIONAL'],
            // Row 5: Summary Table Column Headers (Merged A5:C5, D5:E5, F5:H5)
            ['Nama Indikator / Metrik', '', '', 'Nilai Akumulasi', '', 'Keterangan Analisis'],
            // Rows 6-10: Summary Metric Rows
            ['Total Kerugian Transaksi', '', '', $totalLost, '', 'Estimasi potensi omzet yang terhenti akibat pembatalan SPK'],
            ['Jumlah SPK Dibatalkan', '', '', $totalOrdersCount . ' SPK', '', 'Total unit pesanan yang dibatalkan pada periode ini'],
            ['Total Uang Masuk dari Customer', '', '', $totalPaid, '', 'Akumulasi pembayaran uang yang telah diterima dari customer'],
            ['Total Dana Refund (Pengembalian)', '', '', $totalRefund, '', 'Total uang yang dikembalikan / dialokasikan kembali ke customer'],
            ['Total Sisa Kas Tertahan Perusahaan', '', '', $totalSisaKas, '', 'Selisih kas perusahaan (Total Uang Masuk dikurangi Total Refund)'],
            // Row 11: Blank
            [''],
            // Row 12: Section Header Data (Merged A12:L12)
            ['RINCIAN DATA TRANSAKSI BATAL'],
            // Row 13: Data Table Headers
            [
                'No.',
                'No. SPK',
                'Tanggal Batal',
                'Nama Pelanggan',
                'No. WhatsApp',
                'Detail Sepatu',
                'Estimasi Kerugian',
                'Uang Masuk Customer',
                'Nominal Refund',
                'Sisa Kas Tertahan',
                'Alasan Pembatalan',
                'Catatan Tambahan Refund'
            ]
        ];

        // 4. Populate Data Rows (Starting at row 14)
        $currentRow = 14;
        $this->dataStartRow = $currentRow;

        if ($orders->isEmpty()) {
            $rows[] = ['1', '-', '-', 'Tidak ada data transaksi batal pada filter ini.', '-', '-', 0, 0, 0, 0, '-', '-'];
            $this->dataEndRow = $currentRow;
            $currentRow++;
        } else {
            $no = 1;
            foreach ($orders as $order) {
                $paidSoFar = (float) ($order->invoice?->paid_amount ?? $order->payments->sum('amount_total'));
                $refundAmt = (float) ($order->refund_amount ?? 0);
                $sisaKas = max(0, $paidSoFar - $refundAmt);

                if ($refundAmt > 0) {
                    $this->refundRows[] = $currentRow;
                }
                if ($paidSoFar > 0) {
                    $this->paidRows[] = $currentRow;
                }

                $shoeDetail = trim(($order->shoe_brand ?: '') . ' ' . ($order->shoe_type ?: ''));
                if ($order->shoe_color) {
                    $shoeDetail .= ' (Warna: ' . $order->shoe_color . ')';
                }

                $rows[] = [
                    $no++,
                    $order->spk_number,
                    $order->updated_at ? $order->updated_at->format('d/m/Y H:i') : '-',
                    $order->customer_name ?: ($order->customer->name ?? '-'),
                    $order->customer_phone ?: ($order->customer->phone ?? '-'),
                    $shoeDetail ?: 'Sepatu',
                    (float) $order->total_transaksi,
                    $paidSoFar,
                    $refundAmt,
                    $sisaKas,
                    $order->reception_rejection_reason ?: 'Tidak dicantumkan',
                    $order->refund_notes ?: '-'
                ];

                $currentRow++;
            }
            $this->dataEndRow = $currentRow - 1;
        }

        // 5. Total Row at bottom (Merged A:E)
        $this->summaryRowIndex = $currentRow;
        $rows[] = [
            'TOTAL AKUMULASI',
            '',
            '',
            '',
            '',
            $totalOrdersCount . ' SPK',
            $totalLost,
            $totalPaid,
            $totalRefund,
            $totalSisaKas,
            '',
            ''
        ];

        // 6. Footer Notes
        $rows[] = [''];
        $rows[] = ['* Catatan: Data ini diambil secara realtime dari Sistem Workshop Shoeworkshop (Modul Keuangan). Angka bersifat akuntabel.'];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Merge Title and Section Headers
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A4:H4');
        $sheet->mergeCells('A12:L12');

        // Summary Metric Table Merges (Rows 5 to 10)
        for ($r = 5; $r <= 10; $r++) {
            $sheet->mergeCells("A{$r}:C{$r}");
            $sheet->mergeCells("D{$r}:E{$r}");
            $sheet->mergeCells("F{$r}:H{$r}");
        }

        // Merge summary row title: A to E
        if ($this->summaryRowIndex > 0) {
            $sheet->mergeCells("A{$this->summaryRowIndex}:E{$this->summaryRowIndex}");
        }

        // 2. Set Row Heights for generous breathing room
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(24);
        $sheet->getRowDimension(5)->setRowHeight(24);
        for ($r = 6; $r <= 10; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(22);
        }
        $sheet->getRowDimension(12)->setRowHeight(26);
        $sheet->getRowDimension(13)->setRowHeight(32); // Table header
        for ($r = $this->dataStartRow; $r <= $this->dataEndRow; $r++) {
            $sheet->getRowDimension($r)->setRowHeight(24);
        }
        if ($this->summaryRowIndex > 0) {
            $sheet->getRowDimension($this->summaryRowIndex)->setRowHeight(28);
        }

        // 3. Format Base Styles
        $styles = [
            // Row 1: Document Title
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0F172A']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT]
            ],
            // Row 2: Subtitle / Meta
            2 => [
                'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT]
            ],
            // Row 4: Summary Card Header
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F172A'] // Dark Ink
                ]
            ],
            // Row 5: Summary Table Column Header
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '334155'] // Slate 700
                ]
            ],
            // Row 12: Table Section Title
            12 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22B086'] // Shoeworkshop Emerald
                ]
            ],
            // Row 13: Main Table Column Header
            13 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'] // Dark Slate
                ]
            ]
        ];

        // 4. Style Summary Rows (Rows 6 to 10)
        for ($r = 6; $r <= 10; $r++) {
            $sheet->getStyle("A{$r}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('334155');
            $sheet->getStyle("D{$r}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('0F172A');
            $sheet->getStyle("F{$r}")->getFont()->setSize(9)->getColor()->setRGB('64748B');

            $sheet->getStyle("A{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("D{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F{$r}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // Borders
            $sheet->getStyle("A{$r}:H{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

            // Format monetary summary values
            if (in_array($r, [6, 8, 9, 10])) {
                $sheet->getStyle("D{$r}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
            }
        }

        // 5. Data Rows Formatting (Rows 14 to End)
        for ($r = $this->dataStartRow; $r <= $this->dataEndRow; $r++) {
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("G{$r}:J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("K{$r}:L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

            // Format numbers to clean currency (NO green warning triangles!)
            $sheet->getStyle("G{$r}:J{$r}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            // Subtle Zebra striping
            if ($r % 2 === 1) {
                $sheet->getStyle("A{$r}:L{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            // Grid borders
            $sheet->getStyle("A{$r}:L{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
        }

        // 6. Conditional Formatting: Highlight Rows with Refund (Soft Amber)
        foreach ($this->refundRows as $row) {
            $sheet->getStyle("I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
            $sheet->getStyle("I{$row}")->getFont()->setBold(true)->getColor()->setRGB('92400E');
        }

        // 7. Conditional Formatting: Highlight Rows with Uang Masuk (Soft Emerald)
        foreach ($this->paidRows as $row) {
            $sheet->getStyle("H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');
            $sheet->getStyle("H{$row}")->getFont()->setBold(true)->getColor()->setRGB('065F46');
        }

        // 8. Summary Row Styling at bottom
        if ($this->summaryRowIndex > 0) {
            $sRow = $this->summaryRowIndex;
            $styles[$sRow] = [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0F172A']],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9']
                ]
            ];
            $sheet->getStyle("A{$sRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("F{$sRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("G{$sRow}:J{$sRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("G{$sRow}:J{$sRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            $sheet->getStyle("A{$sRow}:L{$sRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('94A3B8');
            $sheet->getStyle("A{$sRow}:L{$sRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->getColor()->setRGB('0F172A');
        }

        return $styles;
    }
}
