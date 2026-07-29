<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CsAnalyticsExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $overview;
    protected $pathAnalysis;
    protected $startDate;
    protected $endDate;
    protected $selectedCs;

    public function __construct($overview, $pathAnalysis, $startDate, $endDate, $selectedCs)
    {
        $this->overview = $overview;
        $this->pathAnalysis = $pathAnalysis;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->selectedCs = $selectedCs;
    }

    public function array(): array
    {
        $csLabel = $this->selectedCs ? $this->selectedCs->name : 'Keseluruhan (Global)';
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');

        return [
            // Row 1: Main Header
            ['SHOE WORKSHOP - LAPORAN KINERJA & ANALITIK CS'],
            // Row 2: Filter Info
            ['Periode Laporan:', $rangeLabel, 'Akun CS:', $csLabel],
            // Row 3: Blank separator (using a space to prevent skipping)
            [''],
            
            // Row 4: Section 1 Header
            ['1. GLOBAL OVERVIEW METRICS'],
            // Row 5: Column Headers
            ['Nama Metrik', 'Nilai Metrik', 'Keterangan'],
            // Row 6 - 10: Overview Metrics
            ['Total Lead Intake', $this->overview['total_leads'], 'Input periode ini'],
            ['Total Closing', $this->overview['total_closings'], $this->overview['conversion_rate'] . '% Conversion Rate'],
            ['Total Sepatu Masuk', $this->overview['total_incoming_items'], 'Volume fisik masuk'],
            ['Revenue Realization', 'Rp ' . number_format($this->overview['total_revenue'], 0, ',', '.'), 'Omset closing valid'],
            ['Avg Deal Value', 'Rp ' . number_format($this->overview['avg_deal_value'], 0, ',', '.'), 'Rata-rata per deal'],
            
            // Row 11: Blank separator
            [''],
            
            // Row 12: Section 2 Header
            ['2. CLOSING PATH ANALYSIS'],
            // Row 13: Column Headers
            ['Nama Langkah/Jalur', 'Nilai Metrik', 'Keterangan'],
            // Row 14 - 17: Closing Path metrics
            ['Closing Langsung', $this->pathAnalysis['closed_direct'], 'Konsultasi -> Closing (Tanpa Follow-up)'],
            ['Closing via Follow-up', $this->pathAnalysis['closed_via_followup'], 'Konsultasi -> Follow-up -> Closing'],
            ['Konsultasi -> Follow-up', $this->pathAnalysis['total_to_followup'], 'Total lead masuk Follow-up (Efektivitas: ' . $this->pathAnalysis['followup_effectiveness'] . '%)'],
            ['Follow-up Aktif', $this->pathAnalysis['active_followup'], 'Live Count'],
            
            // Row 18: Blank separator
            [''],
            // Row 19: Generated meta
            ['Laporan di-generate pada:', date('d/m/Y H:i')]
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for headers
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A4:C4');
        $sheet->mergeCells('A12:C12');

        // Set horizontal alignment
        $sheet->getStyle('B6:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B14:B17')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->getStyle('A5:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('A13:C13')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        return [
            // Row 1: Title styling
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1e293b']],
            ],
            // Row 2: Metadata styling
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '475569']],
            ],
            // Row 4: Section 1 Header styling
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85'] // Brand Green
                ]
            ],
            // Row 5: Heading Columns styling
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569'] // Dark Slate
                ]
            ],
            // Row 12: Section 2 Header styling
            12 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff'], 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '22AF85'] // Brand Green
                ]
            ],
            // Row 13: Heading Columns styling
            13 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'ffffff']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '475569'] // Dark Slate
                ]
            ],
            // Row 19: Footer info styling
            19 => [
                'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '94a3b8']],
            ]
        ];
    }
}
