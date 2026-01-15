<?php

namespace App\Exports;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReceptionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return WorkOrder::where('status', WorkOrderStatus::DITERIMA->value)
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. SPK',
            'Nama Customer',
            'No. WhatsApp',
            'Email',
            'Alamat',
            'Brand Sepatu',
            'Ukuran',
            'Warna',
            'Tanggal Masuk',
            'Estimasi Selesai',
            'Prioritas',
            'Status',
            'Lokasi',
            'Dibuat Oleh',
            'Tanggal Dibuat',
        ];
    }

    /**
     * @param mixed $order
     * @return array
     */
    public function map($order): array
    {
        return [
            $order->spk_number,
            $order->customer_name,
            $order->customer_phone,
            $order->customer_email ?? '-',
            $order->customer_address ?? '-',
            $order->shoe_brand,
            $order->shoe_size,
            $order->shoe_color,
            $order->entry_date ? $order->entry_date->format('Y-m-d') : '-',
            $order->estimation_date ? $order->estimation_date->format('Y-m-d') : '-',
            $order->priority,
            $order->status,
            $order->current_location,
            $order->creator ? $order->creator->name : '-',
            $order->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
