<?php

namespace App\Exports;

use App\Models\CsLead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CsLeadsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = CsLead::with('cs');

        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['source']) && $this->filters['source']) {
            $query->where('source', $this->filters['source']);
        }

        if (isset($this->filters['priority']) && $this->filters['priority']) {
            $query->where('priority', $this->filters['priority']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Phone',
            'Email',
            'Status',
            'Priority',
            'Source',
            'CS Handler',
            'Entry Date',
            'Response Time (Min)',
            'Expected Value',
            'Notes',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->customer_name,
            $lead->customer_phone,
            $lead->customer_email,
            $lead->status,
            $lead->priority,
            $lead->source,
            $lead->cs->name ?? 'N/A',
            $lead->created_at->format('Y-m-d H:i'),
            $lead->response_time_minutes,
            $lead->expected_value,
            $lead->notes,
        ];
    }
}
