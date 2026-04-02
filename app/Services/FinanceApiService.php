<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinanceApiService
{
    /**
     * Get finance sync data matching legacy criteria.
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function getFinanceSyncData(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = Invoice::with('customer')
            ->orderBy('created_at', 'DESC');

        // Match legacy default: created_at > '2026-02-01 00:00:00'
        if (!$startDate) {
            $query->where('created_at', '>', '2026-02-01 00:00:00');
        } else {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->get();
    }
}
