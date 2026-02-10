<?php

namespace App\Exports;

use App\Models\WorkOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinanceMonthlyExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithStyles
{
    protected $tab;
    protected $search;
    protected $date_from;
    protected $date_to;

    public function __construct($tab = 'completed', $search = null, $date_from = null, $date_to = null)
    {
        $this->tab = $tab;
        $this->search = $search;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = WorkOrder::query()->with(['payments', 'customer']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('spk_number', 'like', "%{$this->search}%")
                  ->orWhere('customer_name', 'like', "%{$this->search}%")
                  ->orWhere('customer_phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->date_from) {
            $query->where(function($q) {
                $q->whereDate('finance_entry_at', '>=', $this->date_from)
                  ->orWhere(function($sq) {
                      $sq->whereNull('finance_entry_at')->whereDate('created_at', '>=', $this->date_from);
                  });
            });
        }

        if ($this->date_to) {
            $query->where(function($q) {
                $q->whereDate('finance_entry_at', '<=', $this->date_to)
                  ->orWhere(function($sq) {
                      $sq->whereNull('finance_entry_at')->whereDate('created_at', '<=', $this->date_to);
                  });
            });
        }

        // Apply Tab Logic (Similar to FinanceController)
        switch ($this->tab) {
            case 'completed':
                $query->where('sisa_tagihan', '<=', 0)->where('total_transaksi', '>', 0);
                break;
            case 'waiting_dp':
                $query->whereIn('status', [
                    \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_VERIFICATION->value,
                ]);
                break;
            case 'in_progress':
                $query->where(function($q) {
                    $q->where('sisa_tagihan', '>', 0)->orWhereNull('sisa_tagihan');
                })->whereNotIn('status', [
                    \App\Enums\WorkOrderStatus::WAITING_PAYMENT->value,
                    \App\Enums\WorkOrderStatus::WAITING_VERIFICATION->value,
                    \App\Enums\WorkOrderStatus::SELESAI->value,
                    \App\Enums\WorkOrderStatus::DIANTAR->value,
                    \App\Enums\WorkOrderStatus::DONASI->value
                ]);
                break;
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No SPK',
            'Nama Customer',
            'CS Code',
            'Total Transaksi',
            'Total Terbayar',
            'Sisa Tagihan',
            'Status Transaksi',
            'Status Pembayaran',
            'Tanggal Masuk Finance',
        ];
    }

    /**
    * @var WorkOrder $order
    */
    public function map($order): array
    {
        $totalPaid = $order->payments->sum('amount_total');
        $sisa = $order->total_transaksi - $totalPaid;
        
        $statusPembayaran = 'Belum Lunas';
        if ($sisa <= 0 && $order->total_transaksi > 0) {
            $statusPembayaran = 'Lunas';
        } elseif ($totalPaid > 0) {
            $statusPembayaran = 'DP/Cicil';
        }

        return [
            $order->spk_number,
            $order->customer_name,
            $order->cs_code ?? '-',
            $order->total_transaksi,
            $totalPaid,
            $sisa,
            str_replace('_', ' ', $order->status->name),
            $statusPembayaran,
            $order->finance_entry_at ? $order->finance_entry_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0',
            'E' => '#,##0',
            'F' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '22AF85']]],
        ];
    }
}
