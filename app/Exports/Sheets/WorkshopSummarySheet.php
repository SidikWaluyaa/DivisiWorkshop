<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class WorkshopSummarySheet implements FromCollection, WithTitle, WithHeadings, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection([
            // Performance Metrics
            ['METRIK PERFORMA UTAMA'],
            ['Total Pendapatan', 'Rp ' . number_format($this->data['revenue'], 0, ',', '.')],
            ['Total Order Selesai', $this->data['throughput'] . ' Unit'],
            ['Rata-rata Waktu Selesai', $this->data['avgCompletionTime'] . ' Hari'],
            ['Tingkat Ketepatan Waktu', $this->data['onTimeRate'] . '%'],
            ['Tingkat Lolos QC (Fpy)', $this->data['qcPassRate'] . '%'],
            [''], 
            [''],
            
            // Top Technicians
            ['TOP 5 TEKNISI (QC Output)'],
            ['Nama Teknisi', 'Jumlah Selesai'],
        ] + $this->data['topPerformers']->map(function($user) {
            return [$user->name, $user->completed_count . ' Unit'];
        })->toArray() + [
            [''],
            [''],
            // Top Services
            ['LAYANAN TERPOPULER (Revenue)'],
            ['Nama Layanan', 'Total Pendapatan', 'Jumlah Order'],
        ] + $this->data['serviceMix']->map(function($mix) {
            return [$mix->service->name, 'Rp ' . number_format($mix->total_revenue, 0, ',', '.'), $mix->order_count];
        })->toArray());
    }

    public function headings(): array
    {
        return [
            ['LAPORAN PERFORMA WORKSHOP'],
            ['Periode:', $this->data['startDate'] . ' - ' . $this->data['endDate']],
            ['Export Date:', now()->format('d M Y H:i')],
            [''],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Eksekutif';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            5 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF008080']]], // Header Metrik
            13 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF008080']]], // Header Teknisi
            
            // Dynamic styling based on rows count might be needed, but this is simple enough
        ];
    }
}
