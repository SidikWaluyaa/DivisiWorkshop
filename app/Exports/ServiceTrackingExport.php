<?php

namespace App\Exports;

use App\Models\WorkOrderService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiceTrackingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected array $filters;
    private int $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = WorkOrderService::query()
            ->whereHas('workOrder', function($q) {
                $q->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING->value);
            })
            ->with(['workOrder', 'service', 'technician']);

        if (!empty($this->filters['date_start'])) {
            $query->where('created_at', '>=', Carbon::parse($this->filters['date_start'])->startOfDay());
        }
        if (!empty($this->filters['date_end'])) {
            $query->where('created_at', '<=', Carbon::parse($this->filters['date_end'])->endOfDay());
        }

        if (!empty($this->filters['category'])) {
            $category = $this->filters['category'];
            $query->where(function($q) use ($category) {
                $q->where('category_name', $category)
                  ->orWhereHas('service', function($sq) use ($category) {
                      $sq->where('category', $category);
                  });
            });
        }

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('custom_service_name', 'like', '%' . $search . '%')
                  ->orWhere('category_name', 'like', '%' . $search . '%')
                  ->orWhereHas('service', function($sq) use ($search) {
                      $sq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('workOrder', function($wq) use ($search) {
                      $wq->where('spk_number', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%');
                  });
            });
        }

        return $query->latest('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL SPK',
            'NOMOR SPK',
            'NAMA KUSTUMER',
            'KATEGORI JASA',
            'NAMA JASA',
            'TEKNISI',
            'BIAYA JASA (RP)',
            'STATUS SPK',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;

        $categoryName = $item->category_name 
            ?: ($item->service->category ?? 'Jasa Custom');

        $serviceName = $item->custom_service_name 
            ?: ($item->service->name ?? 'Jasa Perbaikan');

        $technicianName = $item->technician->name ?? '-';

        return [
            $this->rowNumber,
            $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
            $item->workOrder->spk_number ?? '-',
            $item->workOrder->customer_name ?? '-',
            strtoupper($categoryName),
            $serviceName,
            $technicianName,
            (float) $item->cost,
            $item->workOrder->status->value ?? ($item->workOrder->status ?? '-'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0F766E'] // Teal theme
                ]
            ],
        ];
    }
}
