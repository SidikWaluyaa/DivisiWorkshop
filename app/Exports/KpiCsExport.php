<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KpiCsExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected array $csSummary;
    protected $startDate;
    protected $endDate;

    public function __construct(array $csSummary, $startDate, $endDate)
    {
        $this->csSummary = $csSummary;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $overview = $this->csSummary['overview'];
        $path = $this->csSummary['path_analysis'];
        $channel = $this->csSummary['channel_stats'];
        $leaderboard = $this->csSummary['leaderboard'];
        $cards = $this->csSummary['summary_cards'];

        $rows = [];

        // Title
        $rows[] = ['LAPORAN KINERJA & HASIL CS (KPI CS)'];
        $rows[] = ['Periode Tanggal:', $this->startDate->format('d/m/Y') . ' s/d ' . $this->endDate->format('d/m/Y')];
        $rows[] = [];

        // Section 1: Overview
        $rows[] = ['SECTION 1: GLOBAL OVERVIEW METRICS'];
        $rows[] = ['Metric', 'Nilai'];
        $rows[] = ['Total Lead Intake (Input Periode Ini)', $overview['total_leads']];
        $rows[] = ['Total Closing (Conversion Rate %)', $overview['total_closings'] . ' (' . $overview['conversion_rate'] . '%)'];
        $rows[] = ['Total Sepatu Masuk (Volume Fisik)', $overview['total_incoming_items'] . ' Pasang'];
        $rows[] = ['Revenue Realization (Omset Closing Valid)', 'Rp ' . number_format($overview['total_revenue'], 0, ',', '.')];
        $rows[] = ['Avg Deal Value (Rata-rata Per Deal)', 'Rp ' . number_format($overview['avg_deal_value'], 0, ',', '.')];
        $rows[] = [];

        // Section 2: Path Analysis & Summary Cards
        $rows[] = ['SECTION 2: CLOSING PATH ANALYSIS & LOGISTIK CS'];
        $rows[] = ['Indikator', 'Hasil'];
        $rows[] = ['Closing Langsung (Konsultasi -> Closing)', $path['closed_direct']];
        $rows[] = ['Closing via Follow-Up', $path['closed_via_followup']];
        $rows[] = ['Total Masuk Follow-Up (Efektivitas %)', $path['total_to_followup'] . ' (' . $path['followup_effectiveness'] . '%)'];
        $rows[] = ['Follow-Up Aktif (Live Count)', $path['active_followup']];
        $rows[] = ['Total Sepatu Diterima', $cards['total_sepatu_diterima'] . ' Pasang (' . $cards['sepatu_diterima_online'] . ' OL / ' . $cards['sepatu_diterima_offline'] . ' OFF)'];
        $rows[] = ['Total SPK Pending', $cards['total_spk_pending'] . ' Pasang'];
        $rows[] = [];

        // Section 3: Channel
        $rows[] = ['SECTION 3: PENJUALAN PER CHANNEL'];
        $rows[] = ['Channel', 'Total Leads', 'Total Closing', 'Revenue', 'Conversion Rate %'];
        $rows[] = ['Online', $channel['ONLINE']['leads'], $channel['ONLINE']['closings'], 'Rp ' . number_format($channel['ONLINE']['revenue'], 0, ',', '.'), $channel['ONLINE']['cr'] . '%'];
        $rows[] = ['Offline', $channel['OFFLINE']['leads'], $channel['OFFLINE']['closings'], 'Rp ' . number_format($channel['OFFLINE']['revenue'], 0, ',', '.'), $channel['OFFLINE']['cr'] . '%'];
        $rows[] = [];

        // Section 4: Leaderboard
        $rows[] = ['SECTION 4: RANGKING EFISIENSI & HASIL CS AGENT'];
        $rows[] = [
            'Rank',
            'CS Agent',
            'Intake (Sepatu Masuk)',
            'Closing (Converted)',
            'Sepatu Diterima',
            'SPK Pending',
            'Batal (Trash)',
            'Revenue',
            'AIO (Psg/Order)'
        ];

        foreach ($leaderboard as $index => $cs) {
            $rows[] = [
                'Rank ' . ($index + 1),
                $cs['cs_name'] . ' (' . $cs['total_leads'] . ' Leads)',
                $cs['incoming_total'] . ' Psg (' . $cs['incoming_online'] . ' OL - ' . $cs['incoming_offline'] . ' OFF)',
                $cs['closings'] . ' Closing (DIR: ' . $cs['closing_direct'] . ' / FU: ' . $cs['closing_via_fu'] . ')',
                $cs['diterima_total'] . ' Psg (' . $cs['diterima_online'] . ' OL - ' . $cs['diterima_offline'] . ' OFF)',
                $cs['spk_pending'] . ' Psg',
                $cs['batal'] . ' Psg',
                'Rp ' . number_format($cs['revenue'], 0, ',', '.'),
                $cs['aio'] . ' PSG/ORDER',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Styling headers
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Find Section Headers dynamically
        $highestRow = $sheet->getHighestRow();
        
        for ($row = 1; $row <= $highestRow; $row++) {
            $val = $sheet->getCell("A{$row}")->getValue();
            if (str_starts_with($val, 'SECTION ')) {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '0F766E']
                    ],
                ]);
            } elseif ($val === 'Metric' || $val === 'Indikator' || $val === 'Channel' || $val === 'Rank') {
                $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F1F5F9']
                    ],
                ]);
            }
        }

        return [];
    }
}
