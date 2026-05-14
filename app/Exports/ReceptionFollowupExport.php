<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReceptionFollowupExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $groupedOrders;
    protected $index = 1;

    public function __construct($groupedOrders)
    {
        $this->groupedOrders = $groupedOrders;
    }

    public function collection()
    {
        return $this->groupedOrders;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA CUSTOMER',
            'WHATSAPP',
            'DAFTAR NOMOR SPK',
            'DETAIL SEPATU',
            'JUMLAH SPK',
            'TANGGAL TERAKHIR'
        ];
    }

    public function map($group): array
    {
        $first = $group->first();
        
        $spkNumbers = $group->pluck('spk_number')->implode("\n");
        
        $shoeDetails = $group->map(function($order) {
            return "{$order->shoe_brand} {$order->shoe_type} ({$order->shoe_color}/{$order->shoe_size})";
        })->implode("\n");

        return [
            $this->index++,
            $first->customer_name,
            $first->customer_phone,
            $spkNumbers,
            $shoeDetails,
            $group->count(),
            $group->max('created_at')->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('D:E')->getAlignment()->setWrapText(true);
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
