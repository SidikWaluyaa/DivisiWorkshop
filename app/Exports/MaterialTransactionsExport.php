<?php

namespace App\Exports;

use App\Models\MaterialTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;

class MaterialTransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = MaterialTransaction::with(['material' => function($q) {
            $q->withTrashed();
        }, 'user'])->latest();

        if (!empty($this->filters['material_id'])) {
            $query->where('material_id', $this->filters['material_id']);
        }

        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tanggal',
            'Waktu',
            'Material',
            'Kategori',
            'Tipe',
            'Kuantitas',
            'Satuan',
            'Harga Satuan',
            'Total Nilai',
            'Saldo Akhir',
            'Operator',
            'Referesi',
            'Catatan',
        ];
    }

    public function map($tx): array
    {
        return [
            $tx->id,
            $tx->created_at->format('d/m/Y'),
            $tx->created_at->format('H:i'),
            $tx->material->name ?? 'Deleted Material',
            $tx->material->category ?? 'N/A',
            $tx->type,
            $tx->quantity,
            $tx->material->unit ?? '',
            $tx->unit_price,
            $tx->total_value,
            $tx->balance_after,
            $tx->user->name ?? 'System',
            $tx->reference_label,
            $tx->notes,
        ];
    }
}
