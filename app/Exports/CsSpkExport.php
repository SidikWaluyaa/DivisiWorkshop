<?php

namespace App\Exports;

use App\Models\CsSpk;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CsSpkExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = CsSpk::with(['lead', 'customer', 'items.workOrder'])
            ->orderBy('created_at', 'desc');

        if (isset($this->filters['date_from']) && $this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to']) && $this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No SPK',
            'Tanggal Dibuat',
            'Nama Customer',
            'No Telepon',
            'Detail Item & Jasa',
            'Total Transaksi',
            'DP Amount',
            'Status SPK'
        ];
    }

    public function map($spk): array
    {
        $itemsDetails = [];
        if ($spk->items && count($spk->items) > 0) {
            foreach ($spk->items as $item) {
                $services = '';
                if (is_array($item->services)) {
                    $services = collect($item->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ');
                } else {
                    $services = $item->services;
                }
                $itemsDetails[] = $item->shoe_brand . " (" . $item->shoe_type . ") - " . $services;
            }
        } elseif ($spk->shoe_brand) {
            $services = '';
            if (is_array($spk->services)) {
                $services = collect($spk->services)->map(fn($s) => is_array($s) ? ($s['name'] ?? '-') : $s)->implode(' • ');
            } else {
                $services = $spk->services;
            }
            $itemsDetails[] = $spk->shoe_brand . " (" . $spk->shoe_type . ") - " . $services;
        }
        $detailJasa = implode("\n", $itemsDetails);

        return [
            $spk->spk_number,
            $spk->created_at->format('d M Y H:i'),
            $spk->lead?->customer_name ?? 'N/A',
            $spk->lead?->customer_phone ?? 'N/A',
            $detailJasa,
            (float) $spk->total_price,
            (float) $spk->dp_amount,
            $spk->label
        ];
    }
}
