<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialRequest;
use App\Models\MaterialTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WarehouseApiService
{
    /**
     * Get all materials with stock and valuation.
     */
    public function getInventoryData(): Collection
    {
        return Material::orderBy('name', 'ASC')->get();
    }

    /**
     * Get material requests with status.
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function getRequestData(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = MaterialRequest::with(['items.material', 'requestedBy'])
            ->orderBy('created_at', 'DESC');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } else {
            // Default to last 60 days to keep sync light
            $query->where('created_at', '>=', now()->subDays(60));
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->get();
    }

    /**
     * Get material transaction ledger.
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function getTransactionData(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = MaterialTransaction::with(['material', 'user'])
            ->orderBy('created_at', 'DESC');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } else {
            // Default to last 30 days for ledger
            $query->where('created_at', '>=', now()->subDays(30));
        }

        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->get();
    }
}
