<?php

namespace App\Exports;

use App\Models\Invoice;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PiutangBeforeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $search;
    protected $status;
    protected $ignoreDate;

    public function __construct($startDate, $endDate, $search = null, $status = 'all', $ignoreDate = true)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $this->endDate = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
        $this->search = $search;
        $this->status = $status;
        $this->ignoreDate = $ignoreDate;
    }

    public function collection()
    {
        $query = Invoice::with(['customer', 'workOrders.workOrderServices.service'])
            ->where('status', '!=', 'Lunas')
            ->whereHas('workOrders', function ($q) {
                $q->whereIn('status', [
                    \App\Enums\WorkOrderStatus::DITERIMA->value,
                    \App\Enums\WorkOrderStatus::READY_TO_DISPATCH->value,
                    \App\Enums\WorkOrderStatus::ASSESSMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_VERIFICATION->value,
                ]);
            });

        if (!$this->ignoreDate && $this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('workOrders', function ($sub) {
                      $sub->where('spk_number', 'like', '%' . $this->search . '%')
                          ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                          ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Nomor SPK',
            'Nama Pelanggan',
            'Nomor WhatsApp/HP',
            'Detail Sepatu',
            'Layanan / Jasa',
            'Total Biaya',
            'Terbayar (DP/Cicil)',
            'Piutang (Outstanding)',
            'Status Pembayaran',
            'Tanggal Invoice',
        ];
    }

    public function map($invoice): array
    {
        $spkList = $invoice->workOrders->pluck('spk_number')->implode(', ');
        
        $shoeList = $invoice->workOrders->map(function($wo) {
            return ($wo->shoe_brand ?: '') . ' ' . ($wo->shoe_type ?: '') . ' (Warna: ' . ($wo->shoe_color ?: '-') . ', Size: ' . ($wo->shoe_size ?: '-') . ')';
        })->implode(' | ');

        $servicesList = $invoice->workOrders->flatMap(function($wo) {
            return $wo->workOrderServices->map(function($svc) {
                return $svc->custom_service_name ?: ($svc->service->name ?? 'Jasa');
            });
        })->unique()->implode(', ');

        return [
            $invoice->invoice_number,
            $spkList,
            $invoice->customer->name ?? 'N/A',
            $invoice->customer->phone ?? 'N/A',
            $shoeList,
            $servicesList ?: '-',
            (double) ($invoice->total_amount + $invoice->shipping_cost - $invoice->discount),
            (double) $invoice->paid_amount,
            (double) $invoice->remaining_balance,
            $invoice->status,
            $invoice->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
