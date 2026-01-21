<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WorkshopOrdersSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function map($order): array
    {
        return [
            $order->spk_number,
            $order->customer_name,
            $order->customer_phone,
            $order->services->pluck('name')->implode(', '),
            'Rp ' . number_format($order->total_service_price, 0, ',', '.'),
            $order->entry_date->format('d/m/Y'),
            $order->estimation_date->format('d/m/Y'),
            $order->finished_date ? $order->finished_date->format('d/m/Y H:i') : '-',
            $order->status->label(),
            $order->qcFinalPic ? $order->qcFinalPic->name : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No. SPK',
            'Nama Customer',
            'No. Telepon',
            'Layanan',
            'Total Harga',
            'Tanggal Masuk',
            'Estimasi Selesai',
            'Tanggal Selesai',
            'Status Akhir',
            'QC Final By',
        ];
    }

    public function title(): string
    {
        return 'Data Order Selesai';
    }
}
