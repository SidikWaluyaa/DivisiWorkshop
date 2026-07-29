<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinanceDashboardExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $summary;
    protected $paymentBreakdown;
    protected $startDate;
    protected $endDate;

    public function __construct($summary, $paymentBreakdown, $startDate, $endDate)
    {
        $this->summary = $summary;
        $this->paymentBreakdown = $paymentBreakdown;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');
        
        $metrics = $this->summary['metrics'] ?? [];
        $statusBreakdown = $this->summary['status_breakdown'] ?? [];

        $totalInvoiced = $metrics['total_invoiced_value'] ?? 0;
        $totalReceived = $metrics['total_cash_received'] ?? 0;
        $totalOutstanding = $metrics['total_outstanding_receivables'] ?? 0;
        $collectionRate = $metrics['collection_rate'] ?? 0;

        $rows = [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN KINERJA KEUANGAN'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section 1 Header
            ['RINGKASAN METRIK UTAMA'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 9: Metrics
            ['Total Nilai Tagihan', 'Rp ' . number_format($totalInvoiced, 0, ',', '.'), 'Nilai tagihan yang diterbitkan pada periode aktif'],
            ['Kas Masuk (Tervalidasi)', 'Rp ' . number_format($totalReceived, 0, ',', '.'), 'Realisasi penerimaan pembayaran'],
            ['Sisa Piutang Aktif', 'Rp ' . number_format($totalOutstanding, 0, ',', '.'), 'Sisa piutang berjalan yang belum tertagih'],
            ['Rasio Penagihan (Collection)', $collectionRate . '%', 'Efektivitas cash flow'],
            
            // Row 10: Blank separator
            [''],
            
            // Row 11: Section 2 Header
            ['DISTRIBUSI STATUS TAGIHAN'],
            // Row 12: Column Headers
            ['Status Tagihan', 'Jumlah Transaksi', 'Total Nominal'],
            
            // Row 13 - 15: Status Breakdown
            [
                'Belum Bayar', 
                ($statusBreakdown['belum_bayar']['count'] ?? 0) . ' Transaksi', 
                'Rp ' . number_format(($statusBreakdown['belum_bayar']['total_amount'] ?? 0), 0, ',', '.')
            ],
            [
                'DP/Cicil', 
                ($statusBreakdown['dp_cicil']['count'] ?? 0) . ' Transaksi', 
                'Rp ' . number_format(($statusBreakdown['dp_cicil']['total_amount'] ?? 0), 0, ',', '.')
            ],
            [
                'Lunas', 
                ($statusBreakdown['lunas']['count'] ?? 0) . ' Transaksi', 
                'Rp ' . number_format(($statusBreakdown['lunas']['total_amount'] ?? 0), 0, ',', '.')
            ],
            
            // Row 16: Blank separator
            [''],
            
            // Row 17: Section 3 Header
            ['DISTRIBUSI TIPE PEMBAYARAN'],
            // Row 18: Column Headers
            ['Tipe Pembayaran', 'Jumlah Transaksi', 'Total Nominal']
        ];

        // Row 19+: Payment Type Breakdown
        $typeLabels = [
            'BEFORE' => 'DP Awal (Before)',
            'AFTER' => 'Pelunasan (After)',
            'TAMBAH_JASA' => 'Tambah Jasa',
            'LUNAS_AWAL' => 'Lunas Awal',
            'ONGKIR' => 'Ongkos Kirim',
            'OTO' => 'Pembayaran Oto',
            'LAINNYA' => 'Lainnya'
        ];

        foreach ($typeLabels as $key => $label) {
            $count = $this->paymentBreakdown[$key]['count'] ?? 0;
            $amount = $this->paymentBreakdown[$key]['total_amount'] ?? 0;
            $rows[] = [
                $label,
                $count . ' Transaksi',
                'Rp ' . number_format($amount, 0, ',', '.')
            ];
        }

        // Blank separator
        $rows[] = [''];
        // Generated meta
        $rows[] = ['Laporan di-generate pada:', date('d/m/Y H:i')];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A11:C11');
        $sheet->mergeCells('A17:C17');

        $totalRows = $sheet->getHighestRow();

        // Set horizontal alignment
        $sheet->getStyle('B6:B9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B13:C15')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B19:C25')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A12:C12')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A18:C18')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

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
            11 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85']
                ]
            ],
            12 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569']
                ]
            ],
            17 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85']
                ]
            ],
            18 => [
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
