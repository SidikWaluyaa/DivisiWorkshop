<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CxWarehouseQcExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rangeLabel = $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y');
        $summary = $this->data['summary'] ?? [];
        $csTable = $this->data['cs_table'] ?? [];
        $dailyTable = $this->data['daily_table'] ?? [];

        $rows = [
            // Row 1: Title
            ['SHOE WORKSHOP - ANALISIS WAREHOUSE QC (QC BEFORE)'],
            ['Periode Laporan:', $rangeLabel],
            ['Tanggal Cetak:', now()->translatedFormat('d F Y H:i:s')],
            [''],

            // Section 1: Ringkasan Metrik
            ['RINGKASAN METRIK WAREHOUSE QC'],
            ['Jumlah Sepatu Masuk', 'Jumlah Lolos', '% Lolos', 'Jumlah Lolos Catatan', '% Lolos Catatan', 'Jumlah Tolak', '% Tolak', 'Total Ter-QC', 'Belum Di-QC'],
            [
                $summary['total_masuk'] ?? 0,
                $summary['total_lolos'] ?? 0,
                ($summary['pct_lolos'] ?? 0) . '%',
                $summary['total_lolos_catatan'] ?? 0,
                ($summary['pct_lolos_catatan'] ?? 0) . '%',
                $summary['total_tolak'] ?? 0,
                ($summary['pct_tolak'] ?? 0) . '%',
                $summary['total_qc'] ?? 0,
                $summary['belum_qc'] ?? 0,
            ],
            [''],

            // Section 2: Rekapan Kontribusi CS QC
            ['REKAPAN KONTRIBUSI CS QC'],
            [
                'Nama CS (Kode)',
                'Jumlah Sepatu Masuk',
                'Lolos',
                '% Lolos',
                'Lolos Dengan Catatan',
                '% Lolos Catatan',
                'Tolak',
                '% Tolak',
                'Jumlah Ter-QC',
                '% Ter-QC',
                'Jml Blm Ter-QC',
                '% Blm Ter-QC'
            ]
        ];

        // Add CS Rows
        foreach ($csTable['rows'] ?? [] as $cs) {
            $rows[] = [
                $cs['display_name'],
                $cs['masuk'],
                $cs['lolos'],
                $cs['pct_lolos'] . '%',
                $cs['lolos_catatan'],
                $cs['pct_lolos_catatan'] . '%',
                $cs['tolak'],
                $cs['pct_tolak'] . '%',
                $cs['ter_qc'],
                $cs['pct_ter_qc'] . '%',
                $cs['blm_qc'],
                $cs['pct_blm_qc'] . '%',
            ];
        }

        // Add CS Total Row
        if (!empty($csTable['total'])) {
            $tot = $csTable['total'];
            $rows[] = [
                $tot['display_name'],
                $tot['masuk'],
                $tot['lolos'],
                $tot['pct_lolos'] . '%',
                $tot['lolos_catatan'],
                $tot['pct_lolos_catatan'] . '%',
                $tot['tolak'],
                $tot['pct_tolak'] . '%',
                $tot['ter_qc'],
                $tot['pct_ter_qc'] . '%',
                $tot['blm_qc'],
                $tot['pct_blm_qc'] . '%',
            ];
        }

        $rows[] = [''];

        // Section 3: Rekapan QC Before Harian
        $rows[] = ['REKAPAN QC BEFORE HARIAN'];
        $rows[] = [
            'Tanggal',
            'Jumlah Sepatu Masuk',
            'Lolos',
            'Lolos Dengan Catatan',
            'Tolak',
            'Jumlah Ter-QC'
        ];

        foreach ($dailyTable['rows'] ?? [] as $d) {
            $rows[] = [
                $d['tanggal'],
                $d['masuk'],
                $d['lolos'],
                $d['lolos_catatan'],
                $d['tolak'],
                $d['ter_qc'],
            ];
        }

        if (!empty($dailyTable['total'])) {
            $totD = $dailyTable['total'];
            $rows[] = [
                $totD['tanggal'],
                $totD['masuk'],
                $totD['lolos'],
                $totD['lolos_catatan'],
                $totD['tolak'],
                $totD['ter_qc'],
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Title Styling
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setBold(true);

        // Section Headers
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A6:I6')->getFont()->setBold(true);
        $sheet->getStyle('A6:I6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E2E8F0');
        $sheet->getStyle('A7:I7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
