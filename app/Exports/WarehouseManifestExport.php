<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class WarehouseManifestExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $summary;
    protected $startDate;
    protected $endDate;

    public function __construct($summary, $startDate, $endDate)
    {
        $this->summary = $summary;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');
        $metrics = $this->summary['metrics'] ?? [];
        $recent = $this->summary['recent_manifests'] ?? [];

        $totalManifests = $metrics['total_manifests_sent'] ?? 0;
        $totalSpk = $metrics['total_spk_sent'] ?? 0;
        $avgSpk = $totalManifests > 0 ? round($totalSpk / $totalManifests, 1) : 0;
        $totalServices = $metrics['total_services_count'] ?? 0;
        $avgServices = $metrics['average_services_per_shoe'] ?? 0;

        $rows = [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN MANIFEST'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel],
            // Row 3: Blank separator
            [''],
            
            // Row 4: Section 1 Header
            ['RINGKASAN METRIK MANIFEST'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 8: Metrics
            ['Total Manifest Terkirim', $totalManifests . ' Manifest', 'Diterima: ' . ($metrics['total_manifests_received'] ?? 0)],
            ['Total SPK / Sepatu Terkirim', $totalSpk . ' Pasang', 'Rerata: ' . $avgSpk . ' Pasang / Manifest'],
            ['Total Jasa Logistik', $totalServices . ' Jasa', 'Rerata Jasa: ' . $avgServices . ' Jasa / Sepatu'],
            
            // Row 9: Blank separator
            [''],
            
            // Row 10: Section 2 Header
            ['DAFTAR MANIFEST TERKIRIM (RECENT MANIFESTS)'],
            // Row 11: Table Headers
            ['No. Manifest', 'Dispatcher / Tanggal', 'Penerima', 'Total SPK', 'Total Jasa', 'Status']
        ];

        // Row 12+: Items
        if (empty($recent)) {
            $rows[] = ['Tidak Ada Manifest Terkirim Pada Periode Ini', '', '', '', '', ''];
        } else {
            foreach ($recent as $manifest) {
                $statusLabel = $manifest['status'] === 'SENT' ? 'Transit' : 'Diterima';
                $rows[] = [
                    $manifest['manifest_number'],
                    $manifest['dispatcher_name'] . ' (' . ($manifest['dispatched_at_formatted'] ?? '-') . ')',
                    $manifest['receiver_name'] ?? '-',
                    $manifest['work_orders_count'] . ' Pasang',
                    $manifest['total_services_count'] . ' Jasa',
                    $statusLabel
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
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A10:F10');

        $totalRows = $sheet->getHighestRow();

        // Set horizontal alignment
        $sheet->getStyle('B6:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D12:F' . ($totalRows - 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A11:F11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

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
            10 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85']
                ]
            ],
            11 => [
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
