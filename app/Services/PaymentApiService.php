<?php

namespace App\Services;

use App\Models\OrderPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PaymentApiService
{
    /**
     * Get payment ledger data for sync.
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Collection
     */
    public function getPaymentSyncData(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = OrderPayment::with(['invoice.customer', 'pic'])
            ->orderBy('paid_at', 'DESC');

        // By default, only show payments from 2026 onwards (matching finance pattern)
        if (!$startDate) {
            $query->where('paid_at', '>', '2026-02-01 00:00:00');
        } else {
            $query->where('paid_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            $query->where('paid_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->get();
    }
}
