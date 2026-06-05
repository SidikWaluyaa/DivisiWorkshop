<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\StorageRack;
use App\Models\Material;
use App\Models\WorkOrderLog;
use App\Models\Purchase;
use App\Models\Shipping;
use App\Models\CsSpk;
use App\Models\CsSpkItem;
use App\Enums\WorkOrderStatus;
use App\Models\WorkshopManifest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarehouseDashboardApiService
{
    /**
     * Get the manifest dashboard summary
     */
    public function getManifestSummary(Carbon $start, Carbon $end, ?string $search = null)
    {
        // Aggregate Core Metrics
        $summaryQuery = DB::table('workshop_manifests')
            ->leftJoin('work_orders', 'workshop_manifests.id', '=', 'work_orders.workshop_manifest_id')
            ->leftJoin('work_order_services', 'work_orders.id', '=', 'work_order_services.work_order_id')
            ->whereBetween('workshop_manifests.dispatched_at', [$start, $end])
            ->whereNull('workshop_manifests.deleted_at')
            ->whereNull('work_orders.deleted_at');

        if ($search) {
            $summaryQuery->where(function($q) use ($search) {
                $q->where('workshop_manifests.manifest_number', 'like', "%{$search}%")
                  ->orWhere('work_orders.spk_number', 'like', "%{$search}%");
            });
        }

        $summary = $summaryQuery->select(
            DB::raw('COUNT(DISTINCT workshop_manifests.id) as total_manifests_sent'),
            DB::raw('COUNT(DISTINCT CASE WHEN workshop_manifests.status = "RECEIVED" THEN workshop_manifests.id END) as total_manifests_received'),
            DB::raw('COUNT(DISTINCT work_orders.id) as total_spk_sent'),
            DB::raw('COUNT(work_order_services.id) as total_services_count')
        )->first();

        // Aggregate Daily Trends
        $dailyQuery = DB::table('workshop_manifests')
            ->leftJoin('work_orders', 'workshop_manifests.id', '=', 'work_orders.workshop_manifest_id')
            ->leftJoin('work_order_services', 'work_orders.id', '=', 'work_order_services.work_order_id')
            ->whereBetween('workshop_manifests.dispatched_at', [$start, $end])
            ->whereNull('workshop_manifests.deleted_at')
            ->whereNull('work_orders.deleted_at');

        if ($search) {
            $dailyQuery->where(function($q) use ($search) {
                $q->where('workshop_manifests.manifest_number', 'like', "%{$search}%")
                  ->orWhere('work_orders.spk_number', 'like', "%{$search}%");
            });
        }

        $dailyTrendsRaw = $dailyQuery->select(
            DB::raw('DATE(workshop_manifests.dispatched_at) as date'),
            DB::raw('COUNT(DISTINCT workshop_manifests.id) as manifests_sent'),
            DB::raw('COUNT(DISTINCT CASE WHEN workshop_manifests.status = "RECEIVED" THEN workshop_manifests.id END) as manifests_received'),
            DB::raw('COUNT(DISTINCT work_orders.id) as spk_sent'),
            DB::raw('COUNT(work_order_services.id) as total_services_count')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        // Map daily trends to fill missing days in the period range
        $labels = [];
        $manifestsSent = [];
        $manifestsReceived = [];
        $spkSent = [];
        $servicesCount = [];
        $dailyTrends = [];

        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            $found = $dailyTrendsRaw->firstWhere('date', $dateStr);

            $mSent = $found ? (int) $found->manifests_sent : 0;
            $mReceived = $found ? (int) $found->manifests_received : 0;
            $sSent = $found ? (int) $found->spk_sent : 0;
            $svcCount = $found ? (int) $found->total_services_count : 0;

            $manifestsSent[] = $mSent;
            $manifestsReceived[] = $mReceived;
            $spkSent[] = $sSent;
            $servicesCount[] = $svcCount;

            $dailyTrends[] = [
                'date' => $dateStr,
                'date_formatted' => $current->format('d M Y'),
                'manifests_sent' => $mSent,
                'manifests_received' => $mReceived,
                'spk_sent' => $sSent,
                'shoes_sent' => $sSent,
                'total_services_count' => $svcCount,
            ];

            $current->addDay();
        }

        // Get Recent Manifests List for the table
        $recentManifestsQuery = WorkshopManifest::with(['dispatcher', 'receiver'])
            ->withCount('workOrders')
            ->whereBetween('dispatched_at', [$start, $end]);

        if ($search) {
            $recentManifestsQuery->where(function($q) use ($search) {
                $q->where('manifest_number', 'like', "%{$search}%")
                  ->orWhereHas('workOrders', fn($wq) => $wq->where('spk_number', 'like', "%{$search}%"))
                  ->orWhereHas('dispatcher', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('receiver', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $recentManifests = $recentManifestsQuery->orderBy('dispatched_at', 'desc')->get()->map(function($m) {
            // Calculate total services count for this manifest
            $servicesCount = DB::table('work_orders')
                ->join('work_order_services', 'work_orders.id', '=', 'work_order_services.work_order_id')
                ->where('work_orders.workshop_manifest_id', $m->id)
                ->whereNull('work_orders.deleted_at')
                ->count();

            return [
                'id' => $m->id,
                'manifest_number' => $m->manifest_number,
                'dispatcher_name' => $m->dispatcher?->name ?? 'N/A',
                'receiver_name' => $m->receiver?->name ?? 'N/A',
                'status' => $m->status,
                'notes' => $m->notes,
                'dispatched_at' => $m->dispatched_at ? $m->dispatched_at->toDateTimeString() : null,
                'dispatched_at_formatted' => $m->dispatched_at ? $m->dispatched_at->format('d M Y H:i') : '-',
                'received_at' => $m->received_at ? $m->received_at->toDateTimeString() : null,
                'received_at_formatted' => $m->received_at ? $m->received_at->format('d M Y H:i') : '-',
                'work_orders_count' => $m->work_orders_count,
                'total_services_count' => (int) $servicesCount,
            ];
        });

        return [
            'metrics' => [
                'total_manifests_sent' => (int) $summary->total_manifests_sent,
                'total_manifests_received' => (int) $summary->total_manifests_received,
                'total_spk_sent' => (int) $summary->total_spk_sent,
                'total_shoes_sent' => (int) $summary->total_spk_sent,
                'total_services_count' => (int) $summary->total_services_count,
                'average_services_per_shoe' => $summary->total_spk_sent > 0
                    ? (float) round($summary->total_services_count / $summary->total_spk_sent, 1)
                    : 0.0,
            ],
            'chart_data' => [
                'labels' => $labels,
                'manifests_sent' => $manifestsSent,
                'manifests_received' => $manifestsReceived,
                'spk_sent' => $spkSent,
                'services_count' => $servicesCount,
            ],
            'daily_trends' => $dailyTrends,
            'recent_manifests' => $recentManifests,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'last_updated' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the sortir dashboard summary
     */
    public function getSortirSummary(Carbon $start, Carbon $end, ?string $search = null, bool $overdueOnly = false)
    {
        // Query all work orders currently in SORTIR status
        $query = WorkOrder::where('status', WorkOrderStatus::SORTIR);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        $workOrders = $query->orderBy('updated_at', 'asc')->get();

        $items = [];
        $overdueCount = 0;
        $totalDays = 0;

        foreach ($workOrders as $wo) {
            // Find the date it entered the SORTIR stage
            $enteredAtRaw = DB::table('work_order_logs')
                ->where('work_order_id', $wo->id)
                ->where('step', 'SORTIR')
                ->where('action', 'STATUS_CHANGE')
                ->orderBy('created_at', 'desc')
                ->value('created_at');

            // Fallback if log doesn't exist
            $enteredAt = $enteredAtRaw ? Carbon::parse($enteredAtRaw) : ($wo->waktu ?? $wo->updated_at);
            
            $days = (int) abs(round(now()->diffInDays($enteredAt)));
            $isOverdue = $days > 3;

            if ($overdueOnly && !$isOverdue) {
                continue;
            }

            if ($isOverdue) {
                $overdueCount++;
            }
            $totalDays += $days;

            $items[] = [
                'id' => $wo->id,
                'spk_number' => $wo->spk_number,
                'customer_name' => $wo->customer_name,
                'shoe_brand' => $wo->shoe_brand ?? '-',
                'shoe_type' => $wo->shoe_type ?? '',
                'entered_sortir_at' => $enteredAt->toDateTimeString(),
                'entered_sortir_at_formatted' => $enteredAt->format('d M Y H:i'),
                'days_in_sortir' => $days,
                'is_overdue' => $isOverdue,
                'warning_message' => $isOverdue ? '🚨 TERTAHAN > 3 HARI' : 'ON TRACK',
            ];
        }

        $count = count($items);
        $avgDays = $count > 0 ? round($totalDays / $count, 1) : 0;

        return [
            'metrics' => [
                'total_items_in_sortir' => $count,
                'overdue_items_count' => $overdueCount,
                'average_days_in_sortir' => $avgDays,
            ],
            'items' => $items,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'last_updated' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the full warehouse dashboard summary
     */
    public function getWarehouseSummary(Carbon $start, Carbon $end, ?string $search = null)
    {
        return [
            'metrics' => $this->getHeroMetrics($start, $end),
            'qc_analytics' => [
                'trends' => $this->getQcTrends($start, $end),
                'stats' => $this->getQcStats($start, $end),
            ],
            'efficiency' => $this->getEfficiencyStats($start, $end),
            'inventory' => [
                'value' => $this->getInventoryValue(),
                'material_trends' => $this->getMaterialTrends(),
            ],
            'storage' => [
                'heatmap' => $this->getHeatmapData(),
                'status' => $this->getStorageStats(),
            ],
            'queues' => $this->getQueues($search),
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'last_updated' => now()->toIso8601String(),
        ];
    }

    public function getHeroMetrics(Carbon $start, Carbon $end)
    {
        // 1. Sepatu Masuk (Status DITERIMA ke atas - entry_date)
        // Exclude SPK_PENDING: entry_date diisi saat WO dibuat dari CS handover,
        // tapi baru dihitung "masuk" ketika gudang konfirmasi fisik (status != SPK_PENDING)
        $sepatuMasuk = WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$start, $end])
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING)
            ->count();

        // 2. SPK Print / Otw Ws (Status OTW_WORKSHOP - waktu)
        $spkOtw = WorkOrder::where('status', WorkOrderStatus::OTW_WORKSHOP)
            ->whereBetween('waktu', [$start, $end])
            ->count();

        // 3. SPK Tertahan / QC Reject (Historical Rejections in this Period)
        $qcReject = WorkOrderLog::where('action', 'QC_REJECTED')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // 4. After Masuk (Status SELESAI - finished_date)
        $afterMasuk = WorkOrder::whereNotNull('finished_date')
            ->whereBetween('finished_date', [$start, $end])
            ->count();

        // 5. Sepatu Keluar (taken_date filled)
        $sepatuKeluar = WorkOrder::whereNotNull('taken_date')
            ->whereBetween('taken_date', [$start, $end])
            ->count();

        // 6. Total Sepatu di Gudang (Split into Inbound and Finish)
        $inboundInventory = StorageRack::active()
            ->where('category', \App\Enums\StorageCategory::BEFORE)
            ->sum('current_count');

        $finishInventory = StorageRack::active()
            ->whereNotIn('category', [
                \App\Enums\StorageCategory::BEFORE,
                \App\Enums\StorageCategory::ACCESSORIES
            ])
            ->sum('current_count');

        // 7. Clearance Rate Before (Inbound flow balance)
        $clearanceRateBefore = $sepatuMasuk > 0 
            ? (($spkOtw - $sepatuMasuk) / $sepatuMasuk) * 100 
            : ($spkOtw > 0 ? 100.0 : 0.0);

        // 8. Clearance Rate After (Outbound flow balance)
        $clearanceRateAfter = $afterMasuk > 0 
            ? (($sepatuKeluar - $afterMasuk) / $afterMasuk) * 100 
            : ($sepatuKeluar > 0 ? 100.0 : 0.0);

        return [
            // Status Saat Ini (Snapshot)
            'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
            'finished_not_stored' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->count(),
            'stored_items' => StorageRack::active()
                ->where('category', '!=', \App\Enums\StorageCategory::ACCESSORIES)
                ->sum('current_count'), 
            'shipping_pending' => Shipping::where('is_verified', false)->count(),
            'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->count(),
            'shoes_in_rack_count' => \App\Models\StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
                ->stored()
                ->count(),
            'donation_candidates_count' => \App\Models\StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
                ->stored()
                ->where('stored_at', '<=', now()->subMonths(3))
                ->count(),
            
            // Performa Periode & Scoreboard Baru (M1 - M9)
            'incoming_day' => $sepatuMasuk, 
            'spk_print' => $spkOtw,
            'qc_reject' => $qcReject,
            'after_masuk' => $afterMasuk,
            'sepatu_keluar' => $sepatuKeluar,
            'inbound_inventory' => $inboundInventory,
            'finish_inventory' => $finishInventory,
            'total_inventory' => $inboundInventory + $finishInventory,
            'clearance_rate_before' => round($clearanceRateBefore, 1),
            'clearance_rate_after' => round($clearanceRateAfter, 1),
            
            // Tambahan Analytics (Bypass / Compatibility)
            'finished_day' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereBetween('updated_at', [$start, $end])
                ->count(),
            'total_incoming' => WorkOrder::whereBetween('created_at', [$start, $end])->count(),
            'total_finished' => WorkOrder::where('status', WorkOrderStatus::SELESAI)->whereBetween('updated_at', [$start, $end])->count(),
            'total_qc_processed' => WorkOrder::whereNotNull('warehouse_qc_status')->whereBetween('warehouse_qc_at', [$start, $end])->count(),
        ];
    }

    /**
     * Get Daily Flow Metrics for Charts and Audit Tables
     */
    public function getDailyFlowMetrics(Carbon $start, Carbon $end)
    {
        $inbound = WorkOrder::whereNotNull('entry_date')
            ->whereBetween('entry_date', [$start, $end])
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING)
            ->select(DB::raw('DATE(entry_date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $otw = WorkOrder::where('status', WorkOrderStatus::OTW_WORKSHOP)
            ->whereBetween('waktu', [$start, $end])
            ->select(DB::raw('DATE(waktu) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        // Audit daily rejections historically using WorkOrderLog action QC_REJECTED
        $reject = WorkOrderLog::where('action', 'QC_REJECTED')
            ->whereBetween('created_at', [$start, $end])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $after = WorkOrder::whereNotNull('finished_date')
            ->whereBetween('finished_date', [$start, $end])
            ->select(DB::raw('DATE(finished_date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $outbound = WorkOrder::whereNotNull('taken_date')
            ->whereBetween('taken_date', [$start, $end])
            ->select(DB::raw('DATE(taken_date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $labels = [];
        $sepatuMasuk = [];
        $spkOtw = [];
        $qcReject = [];
        $afterMasuk = [];
        $sepatuKeluar = [];
        $clearanceRateBefore = [];
        $clearanceRateAfter = [];
        
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            
            $masukCount = $inbound->firstWhere('date', $dateStr)->count ?? 0;
            $otwCount = $otw->firstWhere('date', $dateStr)->count ?? 0;
            $rejectCount = $reject->firstWhere('date', $dateStr)->count ?? 0;
            $afterCount = $after->firstWhere('date', $dateStr)->count ?? 0;
            $keluarCount = $outbound->firstWhere('date', $dateStr)->count ?? 0;
            
            $sepatuMasuk[] = $masukCount;
            $spkOtw[] = $otwCount;
            $qcReject[] = $rejectCount;
            $afterMasuk[] = $afterCount;
            $sepatuKeluar[] = $keluarCount;
            
            // Inbound Clearance
            $clearanceRateBefore[] = $masukCount > 0 
                ? round((($otwCount - $masukCount) / $masukCount) * 100, 1) 
                : ($otwCount > 0 ? 100.0 : 0.0);
                
            // Outbound Clearance
            $clearanceRateAfter[] = $afterCount > 0 
                ? round((($keluarCount - $afterCount) / $afterCount) * 100, 1) 
                : ($keluarCount > 0 ? 100.0 : 0.0);
            
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'sepatu_masuk' => $sepatuMasuk,
            'spk_otw' => $spkOtw,
            'qc_reject' => $qcReject,
            'after_masuk' => $afterMasuk,
            'sepatu_keluar' => $sepatuKeluar,
            'clearance_before' => $clearanceRateBefore,
            'clearance_after' => $clearanceRateAfter,
            'table_rows' => collect($labels)->map(function($label, $index) use ($sepatuMasuk, $spkOtw, $qcReject, $afterMasuk, $sepatuKeluar, $clearanceRateBefore, $clearanceRateAfter, $start) {
                return [
                    'date' => $label,
                    'full_date' => $start->copy()->addDays($index)->format('Y-m-d'),
                    'sepatu_masuk' => $sepatuMasuk[$index],
                    'spk_otw' => $spkOtw[$index],
                    'qc_reject' => $qcReject[$index],
                    'after_masuk' => $afterMasuk[$index],
                    'sepatu_keluar' => $sepatuKeluar[$index],
                    'clearance_before' => $clearanceRateBefore[$index],
                    'clearance_after' => $clearanceRateAfter[$index],
                ];
            })->reverse()->values()->all()
        ];
    }

    public function getQcTrends(Carbon $start, Carbon $end)
    {
        $lolosTrends = WorkOrder::where('warehouse_qc_status', 'lolos')
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->select(DB::raw('DATE(warehouse_qc_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $rejectTrends = WorkOrder::where('warehouse_qc_status', 'reject')
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->select(DB::raw('DATE(warehouse_qc_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get();

        $labels = [];
        $lolosData = [];
        $rejectData = [];
        
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            
            $foundLolos = $lolosTrends->firstWhere('date', $dateStr);
            $lolosData[] = $foundLolos ? $foundLolos->count : 0;
            
            $foundReject = $rejectTrends->firstWhere('date', $dateStr);
            $rejectData[] = $foundReject ? $foundReject->count : 0;
            
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'lolos' => $lolosData,
            'reject' => $rejectData,
            'total_lolos' => array_sum($lolosData),
            'total_reject' => array_sum($rejectData),
        ];
    }

    public function getQcStats(Carbon $start, Carbon $end)
    {
        $stats = WorkOrder::whereIn('warehouse_qc_status', ['lolos', 'reject'])
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->select('warehouse_qc_status', DB::raw('count(*) as count'))
            ->groupBy('warehouse_qc_status')
            ->get();

        return [
            'labels' => ['QC Lolos', 'QC Reject'],
            'data' => [
                $stats->firstWhere('warehouse_qc_status', 'lolos')->count ?? 0,
                $stats->firstWhere('warehouse_qc_status', 'reject')->count ?? 0,
            ]
        ];
    }

    public function getEfficiencyStats(Carbon $start, Carbon $end)
    {
        $avgDwell = DB::table('storage_assignments')
            ->whereNotNull('retrieved_at')
            ->whereBetween('stored_at', [$start, $end])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, stored_at, retrieved_at)) as avg_hours')
            ->value('avg_hours');

        $processed = WorkOrderLog::whereBetween('created_at', [$start, $end])->count();

        return [
            'avg_dwell_hours' => round($avgDwell ?? 0, 1),
            'total_throughput' => $processed,
            'health_score' => min(100, (max(0, 100 - ($avgDwell ?? 0))))
        ];
    }

    public function getInventoryValue()
    {
        $materials = Material::all();
        $totalValue = $materials->sum(fn($m) => $m->stock * $m->price);
        
        return [
            'total' => $totalValue,
            'by_category' => $materials->groupBy('category')->map(fn($group) => $group->sum(fn($m) => $m->stock * $m->price)),
        ];
    }

    public function getMaterialTrends()
    {
        return [
            'labels' => Material::orderBy('stock', 'desc')->take(5)->pluck('name'),
            'data' => Material::orderBy('stock', 'desc')->take(5)->pluck('stock'),
        ];
    }

    public function getHeatmapData()
    {
        return StorageRack::active()
            ->orderBy('rack_code')
            ->get()
            ->map(function($rack) {
                $usage = ($rack->current_count / max(1, $rack->capacity)) * 100;
                return [
                    'code' => $rack->rack_code,
                    'usage' => round($usage),
                    'color' => $usage > 90 ? 'black' : ($usage > 50 ? 'yellow' : 'green'),
                    'count' => $rack->current_count,
                    'capacity' => $rack->capacity,
                    'category' => $rack->category->value
                ];
            });
    }

    public function getStorageStats()
    {
        return [
            'total_capacity' => StorageRack::active()
                ->where('category', '!=', \App\Enums\StorageCategory::ACCESSORIES)
                ->sum('capacity'),
            'current_usage' => StorageRack::active()
                ->where('category', '!=', \App\Enums\StorageCategory::ACCESSORIES)
                ->sum('current_count'),
            'available_slots' => StorageRack::active()
                ->where('category', '!=', \App\Enums\StorageCategory::ACCESSORIES)
                ->get()
                ->sum(fn($r) => $r->capacity - $r->current_count),
        ];
    }

    public function getQueues(?string $search = null)
    {
        return [
            'reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'needs_qc' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'storage' => WorkOrder::whereIn('status', [WorkOrderStatus::ASSESSMENT, WorkOrderStatus::WAITING_PAYMENT, WorkOrderStatus::SELESAI])
                ->whereDoesntHave('storageAssignments', fn($q) => $q->stored())
                ->where(function($q) {
                    $q->whereNotNull('warehouse_qc_status')->orWhere('status', WorkOrderStatus::SELESAI);
                })
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)
                ->whereNull('taken_date')
                ->whereHas('storageAssignments', fn($q) => $q->stored())
                ->with(['storageAssignments' => fn($q) => $q->stored()])
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'shipping_unverified' => Shipping::where('is_verified', false)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
            'shipping_verified' => Shipping::where('is_verified', true)
                ->when($search, fn($q) => $q->where('spk_number', 'LIKE', "%$search%")->orWhere('customer_name', 'LIKE', "%$search%"))
                ->latest()->take(20)->get(),
        ];
    }
}
