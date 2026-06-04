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

    /**
     * Get Sortir Data with SLA Intelligence (Smart Logic 1)
     */
    public function getSortirIntelligenceData(): Collection
    {
        return \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::SORTIR->value)
            ->with(['materials', 'picSortirSol', 'picSortirUpper'])
            ->get()
            ->map(function ($wo) {
                // Determine Sortir Category
                if ($wo->readyForProduction()->where('id', $wo->id)->exists()) {
                    $wo->sortir_category = 'SIAP PRODUKSI';
                } elseif ($wo->waitingForMaterials()->where('id', $wo->id)->exists()) {
                    $wo->sortir_category = 'IN PROCUREMENT';
                } else {
                    $wo->sortir_category = 'BELUM REQUEST';
                }

                // SLA Calculation (Smart Logic 1)
                // Assuming 'waktu' is updated when entering status
                $entryDate = $wo->waktu ?? $wo->updated_at;
                $wo->days_in_sortir = round(now()->diffInDays($entryDate, false) * -1, 1);
                $wo->is_sla_violated = $wo->days_in_sortir > 3; // Breach if > 3 days

                return $wo;
            });
    }

    /**
     * Material Forecast Logic (Smart Logic 3) 
     * Calculates future demand based on the current Sortir queue.
     */
    public function getMaterialForecastData(): Collection
    {
        // Get all materials needed by WorkOrders in SORTIR status
        return \DB::table('work_order_materials')
            ->join('work_orders', 'work_orders.id', '=', 'work_order_materials.work_order_id')
            ->join('materials', 'materials.id', '=', 'work_order_materials.material_id')
            ->where('work_orders.status', \App\Enums\WorkOrderStatus::SORTIR->value)
            ->where('work_order_materials.status', 'REQUESTED')
            ->select(
                'materials.id',
                'materials.name',
                'materials.unit',
                'materials.stock as current_stock',
                \DB::raw('SUM(work_order_materials.quantity) as total_needed'),
                \DB::raw('(materials.stock - SUM(work_order_materials.quantity)) as forecast_remaining')
            )
            ->groupBy('materials.id', 'materials.name', 'materials.unit', 'materials.stock')
            ->get();
    }

    /**
     * Get invoices with outstanding balance where all work orders are completed.
     */
    public function getPiutangData(?string $startDate = null, ?string $endDate = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\Invoice::with(['customer', 'workOrders.workOrderServices.service'])
            ->where('status', '!=', 'Lunas')
            ->where('spk_status', 'SELESAI');

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Get invoices with outstanding balance where work orders are not yet completed.
     */
    public function getPiutangBeforeData(?string $startDate = null, ?string $endDate = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\Invoice::with(['customer', 'workOrders.workOrderServices.service'])
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

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Get shoe rack data (completed and stored).
     */
    public function getShoeRackData(?string $startDate = null, ?string $endDate = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
            ->stored()
            ->with(['workOrder', 'workOrder.customer']);

        if ($startDate) {
            $query->where('stored_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('stored_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query->orderBy('stored_at', 'DESC')->get();
    }

    /**
     * Get work order services data for tracking.
     */
    public function getServiceTrackingData(?string $startDate = null, ?string $endDate = null, ?string $search = null, ?string $category = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\WorkOrderService::whereHas('workOrder', function($q) {
            $q->where('status', '!=', \App\Enums\WorkOrderStatus::SPK_PENDING->value);
        })->with(['workOrder', 'service', 'technician']);

        if ($startDate) {
            $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        if ($category) {
            $query->where(function($q) use ($category) {
                $q->where('category_name', $category)
                  ->orWhereHas('service', function($sq) use ($category) {
                      $sq->where('category', $category);
                  });
            });
        }

        if ($search) {
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

        return $query->orderBy('created_at', 'DESC')->get();
    }
}

