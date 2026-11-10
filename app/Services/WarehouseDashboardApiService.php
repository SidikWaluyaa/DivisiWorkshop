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

        $recentManifestsRaw = $recentManifestsQuery->orderBy('dispatched_at', 'desc')->get();

        // Batch pre-fetch services count per manifest in 1 query (eliminates N+1 inside map)
        $manifestIds = $recentManifestsRaw->pluck('id')->all();
        $servicesByManifest = !empty($manifestIds)
            ? DB::table('work_orders')
                ->join('work_order_services', 'work_orders.id', '=', 'work_order_services.work_order_id')
                ->whereIn('work_orders.workshop_manifest_id', $manifestIds)
                ->whereNull('work_orders.deleted_at')
                ->selectRaw('work_orders.workshop_manifest_id, COUNT(work_order_services.id) as services_count')
                ->groupBy('work_orders.workshop_manifest_id')
                ->pluck('services_count', 'workshop_manifest_id')
            : collect();

        $recentManifests = $recentManifestsRaw->map(function($m) use ($servicesByManifest) {
            $servicesCount = $servicesByManifest[$m->id] ?? 0;

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
    public function getSortirSummary(Carbon $start, Carbon $end, ?string $search = null, $filter = 'all', ?int $serviceId = null, ?string $category = null, ?string $estStart = null, ?string $estEnd = null)
    {
        // Normalize legacy boolean values to string filters
        if ($filter === true) {
            $filter = 'overdue';
        } elseif ($filter === false) {
            $filter = 'all';
        }

        // Query all work orders currently in SORTIR status
        $query = WorkOrder::where('status', WorkOrderStatus::SORTIR)->with(['workOrderServices.service']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        if ($serviceId) {
            $query->whereHas('workOrderServices', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            });
        }

        if ($category) {
            $query->whereHas('workOrderServices', function($q) use ($category) {
                $q->where('category_name', $category);
            });
        }

        if ($estStart && $estEnd) {
            $query->whereBetween('estimation_date', [
                Carbon::parse($estStart)->startOfDay()->format('Y-m-d'),
                Carbon::parse($estEnd)->endOfDay()->format('Y-m-d')
            ]);
        }

        $workOrders = $query->orderBy('updated_at', 'asc')->get();

        // Batch pre-fetch latest SORTIR entry logs in 1 query (eliminates N+1 inside foreach)
        $woIds = $workOrders->pluck('id')->all();
        $enteredLogs = !empty($woIds)
            ? DB::table('work_order_logs')
                ->whereIn('work_order_id', $woIds)
                ->where('step', 'SORTIR')
                ->where('action', 'STATUS_CHANGE')
                ->selectRaw('work_order_id, MAX(created_at) as latest_entered_at')
                ->groupBy('work_order_id')
                ->pluck('latest_entered_at', 'work_order_id')
            : collect();

        $items = [];
        $overdueCount = 0;
        $totalDays = 0;

        foreach ($workOrders as $wo) {
            // Find the date it entered the SORTIR stage from pre-fetched map
            $enteredAtRaw = $enteredLogs[$wo->id] ?? null;

            // Fallback if log doesn't exist
            $enteredAt = $enteredAtRaw ? Carbon::parse($enteredAtRaw) : ($wo->waktu ?? $wo->updated_at);
            
            $days = (int) abs(round(now()->diffInDays($enteredAt)));
            $isOverdue = $days > 3;
            $isOnTrack = !$isOverdue;

            if ($filter === 'overdue' && !$isOverdue) {
                continue;
            }
            if ($filter === 'on_track' && !$isOnTrack) {
                continue;
            }

            if ($isOverdue) {
                $overdueCount++;
            }
            $totalDays += $days;

            // Estimation Date logic
            $estimationDate = $wo->estimation_date ? Carbon::parse($wo->estimation_date)->startOfDay() : null;
            $hasValidEstimation = $estimationDate && $estimationDate->year > 2000;

            $services = $wo->workOrderServices->map(function($svc) {
                return $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan');
            })->all();

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
                'estimation_date' => $estimationDate ? $estimationDate->toDateString() : null,
                'estimation_date_formatted' => $estimationDate ? $estimationDate->format('d M Y') : 'Belum Set',
                'has_estimation' => $hasValidEstimation,
                'services' => $services,
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
     * Get the production dashboard summary
     */
    public function getProductionSummary(Carbon $start, Carbon $end, ?string $search = null, string $filter = 'all', ?int $serviceId = null, ?string $category = null, ?string $estStart = null, ?string $estEnd = null, string $sort = 'asc', string $limit = '10')
    {
        // Query all work orders currently in PRODUCTION status
        $query = WorkOrder::where('status', WorkOrderStatus::PRODUCTION)->with(['workOrderServices.service']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        if ($serviceId) {
            $query->whereHas('workOrderServices', function($q) use ($serviceId) {
                $q->where('service_id', $serviceId);
            });
        }

        if ($category) {
            $query->whereHas('workOrderServices', function($q) use ($category) {
                $q->where('category_name', $category);
            });
        }

        if ($estStart && $estEnd) {
            $query->whereBetween('estimation_date', [
                Carbon::parse($estStart)->startOfDay()->format('Y-m-d'),
                Carbon::parse($estEnd)->endOfDay()->format('Y-m-d')
            ]);
        }

        $workOrders = $query->orderBy('updated_at', 'asc')->get();

        // Batch pre-fetch latest PRODUCTION entry logs in 1 query (eliminates N+1 inside foreach)
        $woIds = $workOrders->pluck('id')->all();
        $enteredLogs = !empty($woIds)
            ? DB::table('work_order_logs')
                ->whereIn('work_order_id', $woIds)
                ->where('step', 'PRODUCTION')
                ->where('action', 'STATUS_CHANGE')
                ->selectRaw('work_order_id, MAX(created_at) as latest_entered_at')
                ->groupBy('work_order_id')
                ->pluck('latest_entered_at', 'work_order_id')
            : collect();

        $items = [];
        $overdueCount = 0;
        $upcomingCount = 0;
        $totalItemsInProduction = $workOrders->count();

        $today = now()->startOfDay();

        foreach ($workOrders as $wo) {
            // Find the date it entered the PRODUCTION stage from pre-fetched map
            $enteredAtRaw = $enteredLogs[$wo->id] ?? null;

            $enteredAt = $enteredAtRaw ? Carbon::parse($enteredAtRaw) : ($wo->waktu ?? $wo->updated_at);
            $daysInProduction = (int) abs(round(now()->diffInDays($enteredAt)));

            $estimationDate = $wo->estimation_date ? Carbon::parse($wo->estimation_date)->startOfDay() : null;
            $hasValidEstimation = $estimationDate && $estimationDate->year > 2000;

            $isOverdue = false;
            $isUpcoming = false;
            $daysDiff = null;

            if ($hasValidEstimation) {
                if ($estimationDate->lt($today)) {
                    $isOverdue = true;
                    $daysDiff = (int) $today->diffInDays($estimationDate);
                } else {
                    $daysDiff = (int) $today->diffInDays($estimationDate);
                    if ($daysDiff <= 2) {
                        $isUpcoming = true;
                    }
                }
            }

            $isOnTrack = !$isOverdue && !$isUpcoming;

            // Apply filter: 'all', 'overdue', 'upcoming', 'on_track'
            if ($filter === 'overdue' && !$isOverdue) {
                continue;
            }
            if ($filter === 'upcoming' && !$isUpcoming) {
                continue;
            }
            if ($filter === 'on_track' && !$isOnTrack) {
                continue;
            }

            if ($isOverdue) {
                $overdueCount++;
            }
            if ($isUpcoming) {
                $upcomingCount++;
            }

            $services = $wo->workOrderServices->map(function($svc) {
                return $svc->custom_service_name ?? ($svc->service ? $svc->service->name : 'Layanan');
            })->all();

            $items[] = [
                'id' => $wo->id,
                'spk_number' => $wo->spk_number,
                'customer_name' => $wo->customer_name,
                'shoe_brand' => $wo->shoe_brand ?? '-',
                'shoe_type' => $wo->shoe_type ?? '',
                'entered_production_at' => $enteredAt->toDateTimeString(),
                'entered_production_at_formatted' => $enteredAt->format('d M Y H:i'),
                'days_in_production' => $daysInProduction,
                'estimation_date' => $estimationDate ? $estimationDate->toDateString() : null,
                'estimation_date_formatted' => $estimationDate ? $estimationDate->format('d M Y') : 'Belum Set',
                'has_estimation' => $hasValidEstimation,
                'days_diff' => $daysDiff,
                'is_overdue' => $isOverdue,
                'is_upcoming' => $isUpcoming,
                'status_badge' => $isOverdue ? '🚨 OVERDUE' : ($isUpcoming ? '⏰ DUE SOON' : 'ON TRACK'),
                'services' => $services,
            ];
        }

        $rawOverdue = 0;
        $rawUpcoming = 0;
        foreach ($workOrders as $wo) {
            $estDate = $wo->estimation_date ? Carbon::parse($wo->estimation_date)->startOfDay() : null;
            if ($estDate && $estDate->year > 2000) {
                if ($estDate->lt($today)) {
                    $rawOverdue++;
                } elseif ($today->diffInDays($estDate) <= 2) {
                    $rawUpcoming++;
                }
            }
        }

        // Sort items by entered_production_at
        if ($sort === 'desc') {
            usort($items, function($a, $b) {
                return strcmp($b['entered_production_at'], $a['entered_production_at']);
            });
        } else {
            usort($items, function($a, $b) {
                return strcmp($a['entered_production_at'], $b['entered_production_at']);
            });
        }

        // Apply limit after sorting
        if ($limit !== 'all' && is_numeric($limit)) {
            $items = array_slice($items, 0, (int)$limit);
        }

        return [
            'metrics' => [
                'total_items_in_production' => $totalItemsInProduction,
                'overdue_items_count' => $rawOverdue,
                'upcoming_items_count' => $rawUpcoming,
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
     * Get the QC dashboard summary
     */
    public function getQcSummary(Carbon $start, Carbon $end, ?string $search = null, string $filter = 'all', ?string $qcEnteredStart = null, ?string $qcEnteredEnd = null)
    {
        // Query all work orders currently in QC status
        $query = WorkOrder::where('status', WorkOrderStatus::QC);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%");
            });
        }

        $workOrders = $query->orderBy('updated_at', 'asc')->get();

        // Batch pre-fetch latest QC entry logs in 1 query (eliminates N+1 inside foreach)
        $woIds = $workOrders->pluck('id')->all();
        $enteredLogs = !empty($woIds)
            ? DB::table('work_order_logs')
                ->whereIn('work_order_id', $woIds)
                ->where('step', 'QC')
                ->where('action', 'STATUS_CHANGE')
                ->selectRaw('work_order_id, MAX(created_at) as latest_entered_at')
                ->groupBy('work_order_id')
                ->pluck('latest_entered_at', 'work_order_id')
            : collect();

        $items = [];
        $overdueCount = 0;
        $upcomingCount = 0;
        $totalItemsInPeriod = 0;

        $today = now()->startOfDay();

        foreach ($workOrders as $wo) {
            // Find the date it entered the QC stage from pre-fetched map
            $enteredAtRaw = $enteredLogs[$wo->id] ?? null;

            $enteredAt = $enteredAtRaw ? Carbon::parse($enteredAtRaw) : ($wo->waktu ?? $wo->updated_at);

            // Apply QC Entry Date Filter if set
            if ($qcEnteredStart && $qcEnteredEnd) {
                $qcStart = Carbon::parse($qcEnteredStart)->startOfDay();
                $qcEnd = Carbon::parse($qcEnteredEnd)->endOfDay();
                if ($enteredAt->lt($qcStart) || $enteredAt->gt($qcEnd)) {
                    continue;
                }
            }

            $totalItemsInPeriod++;

            $daysInQc = (int) abs(round(now()->diffInDays($enteredAt)));

            $estimationDate = $wo->estimation_date ? Carbon::parse($wo->estimation_date)->startOfDay() : null;
            $hasValidEstimation = $estimationDate && $estimationDate->year > 2000;

            $isOverdue = false;
            $isUpcoming = false;
            $daysDiff = null;

            if ($hasValidEstimation) {
                if ($estimationDate->lt($today)) {
                    $isOverdue = true;
                    $daysDiff = (int) $today->diffInDays($estimationDate);
                } else {
                    $daysDiff = (int) $today->diffInDays($estimationDate);
                    if ($daysDiff <= 2) {
                        $isUpcoming = true;
                    }
                }
            }

            if ($isOverdue) {
                $overdueCount++;
            }
            if ($isUpcoming) {
                $upcomingCount++;
            }

            // Apply status filter: 'all', 'overdue', 'upcoming'
            if ($filter === 'overdue' && !$isOverdue) {
                continue;
            }
            if ($filter === 'upcoming' && !$isUpcoming) {
                continue;
            }

            $items[] = [
                'id' => $wo->id,
                'spk_number' => $wo->spk_number,
                'customer_name' => $wo->customer_name,
                'shoe_brand' => $wo->shoe_brand ?? '-',
                'shoe_type' => $wo->shoe_type ?? '',
                'entered_qc_at' => $enteredAt->toDateTimeString(),
                'entered_qc_at_formatted' => $enteredAt->format('d M Y H:i'),
                'days_in_qc' => $daysInQc,
                'estimation_date' => $estimationDate ? $estimationDate->toDateString() : null,
                'estimation_date_formatted' => $estimationDate ? $estimationDate->format('d M Y') : 'Belum Set',
                'has_estimation' => $hasValidEstimation,
                'days_diff' => $daysDiff,
                'is_overdue' => $isOverdue,
                'is_upcoming' => $isUpcoming,
                'status_badge' => $isOverdue ? '🚨 OVERDUE' : ($isUpcoming ? '⏰ DUE SOON' : 'ON TRACK'),
            ];
        }

        return [
            'metrics' => [
                'total_items_in_qc' => $totalItemsInPeriod,
                'overdue_items_count' => $overdueCount,
                'upcoming_items_count' => $upcomingCount,
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
        $startStr = $start->toDateTimeString();
        $endStr = $end->toDateTimeString();
        $thirtyDaysAgoStr = now()->subDays(30)->toDateTimeString();
        $threeMonthsAgoStr = now()->subMonths(3)->toDateTimeString();
        $rdSql = KamusMetrikQuery::rdSql('work_orders');
        $grSql = "(work_orders.spk_number LIKE '%-GR' OR work_orders.spk_number LIKE '%-GR-%')";

        // 1. Consolidated WorkOrder counts in 1 query
        $woAgg = WorkOrder::selectRaw("
            SUM(CASE WHEN entry_date IS NOT NULL AND entry_date BETWEEN ? AND ? AND status != ? THEN 1 ELSE 0 END) as sepatu_masuk,
            SUM(CASE WHEN finished_date IS NOT NULL AND finished_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as after_masuk,
            SUM(CASE WHEN taken_date IS NOT NULL AND taken_date BETWEEN ? AND ? THEN 1 ELSE 0 END) as sepatu_keluar,
            SUM(CASE WHEN created_at BETWEEN ? AND ? AND {$grSql} THEN 1 ELSE 0 END) as spk_gr_periode,
            SUM(CASE WHEN created_at BETWEEN ? AND ? AND NOT {$grSql} THEN 1 ELSE 0 END) as spk_periode_non_gr,
            SUM(CASE WHEN created_at BETWEEN ? AND ? AND NOT {$grSql} AND warehouse_qc_at IS NOT NULL THEN 1 ELSE 0 END) as spk_tiba_periode,
            SUM(CASE WHEN created_at BETWEEN ? AND ? AND ({$rdSql}) AND NOT {$grSql} THEN 1 ELSE 0 END) as spk_rd_periode,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_reception,
            SUM(CASE WHEN status = ? AND created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as pending_reception_period,
            SUM(CASE WHEN status = ? AND taken_date IS NULL AND finished_date >= ? THEN 1 ELSE 0 END) as siap_kirim_aktif,
            SUM(CASE WHEN status = ? AND taken_date IS NULL AND NOT EXISTS (
                SELECT 1 FROM storage_assignments sa WHERE sa.work_order_id = work_orders.id AND sa.status = 'stored' AND sa.retrieved_at IS NULL
            ) THEN 1 ELSE 0 END) as finished_not_stored,
            SUM(CASE WHEN status = ? AND taken_date IS NULL AND EXISTS (
                SELECT 1 FROM storage_assignments sa WHERE sa.work_order_id = work_orders.id AND sa.status = 'stored' AND sa.retrieved_at IS NULL
            ) THEN 1 ELSE 0 END) as ready_for_pickup,
            SUM(CASE WHEN status = ? AND updated_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as finished_day,
            SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as total_incoming,
            SUM(CASE WHEN warehouse_qc_status IS NOT NULL AND warehouse_qc_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as total_qc_processed
        ", [
            $startStr, $endStr, WorkOrderStatus::SPK_PENDING->value,
            $startStr, $endStr,
            $startStr, $endStr,
            $startStr, $endStr,
            $startStr, $endStr,
            $startStr, $endStr,
            $startStr, $endStr,
            WorkOrderStatus::SPK_PENDING->value,
            WorkOrderStatus::SPK_PENDING->value, $startStr, $endStr,
            WorkOrderStatus::SELESAI->value, $thirtyDaysAgoStr,
            WorkOrderStatus::SELESAI->value,
            WorkOrderStatus::SELESAI->value,
            WorkOrderStatus::SELESAI->value, $startStr, $endStr,
            $startStr, $endStr,
            $startStr, $endStr,
        ])->first();

        $sepatuMasuk = (int) ($woAgg->sepatu_masuk ?? 0);
        $afterMasuk = (int) ($woAgg->after_masuk ?? 0);
        $sepatuKeluar = (int) ($woAgg->sepatu_keluar ?? 0);
        $spkGrPeriode = (int) ($woAgg->spk_gr_periode ?? 0);
        $spkPeriodeNonGr = (int) ($woAgg->spk_periode_non_gr ?? 0);
        $spkTibaPeriode = (int) ($woAgg->spk_tiba_periode ?? 0);
        $spkRdPeriode = (int) ($woAgg->spk_rd_periode ?? 0);

        // 2. Consolidated WorkOrderLog counts in 1 query
        $logAgg = WorkOrderLog::whereBetween('created_at', [$start, $end])
            ->selectRaw("
                COUNT(DISTINCT CASE WHEN step = 'OTW_WORKSHOP' AND action = 'STATUS_CHANGE' THEN work_order_id END) as spk_otw,
                SUM(CASE WHEN action = 'QC_REJECTED' THEN 1 ELSE 0 END) as qc_reject
            ")->first();

        $spkOtw = (int) ($logAgg->spk_otw ?? 0);
        $qcReject = (int) ($logAgg->qc_reject ?? 0);

        // 3. Consolidated StorageRack sums in 1 query
        $rackAgg = StorageRack::active()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN category = ? THEN current_count ELSE 0 END), 0) as inbound_inventory,
                COALESCE(SUM(CASE WHEN category NOT IN (?, ?) THEN current_count ELSE 0 END), 0) as finish_inventory,
                COALESCE(SUM(CASE WHEN category != ? THEN current_count ELSE 0 END), 0) as stored_items
            ", [
                \App\Enums\StorageCategory::BEFORE->value,
                \App\Enums\StorageCategory::BEFORE->value,
                \App\Enums\StorageCategory::ACCESSORIES->value,
                \App\Enums\StorageCategory::ACCESSORIES->value,
            ])->first();

        $inboundInventory = (int) ($rackAgg->inbound_inventory ?? 0);
        $finishInventory = (int) ($rackAgg->finish_inventory ?? 0);

        // 4. Consolidated StorageAssignment counts in 1 query
        $saAgg = \App\Models\StorageAssignment::where('category', \App\Enums\StorageCategory::SHOES->value)
            ->stored()
            ->selectRaw("
                COUNT(*) as shoes_in_rack_count,
                SUM(CASE WHEN stored_at <= ? THEN 1 ELSE 0 END) as donation_candidates_count
            ", [$threeMonthsAgoStr])->first();

        // 5. Clearance Rate Before & After
        $clearanceRateBefore = $sepatuMasuk > 0 
            ? (($spkOtw - $sepatuMasuk) / $sepatuMasuk) * 100 
            : ($spkOtw > 0 ? 100.0 : 0.0);

        $clearanceRateAfter = $afterMasuk > 0 
            ? (($sepatuKeluar - $afterMasuk) / $afterMasuk) * 100 
            : ($sepatuKeluar > 0 ? 100.0 : 0.0);

        $intakeFisik = KamusMetrikQuery::intakeFisik($start, $end);
        $wip = KamusMetrikQuery::wipSnapshot();

        return [
            // Status Saat Ini (Snapshot)
            'pending_reception' => (int) ($woAgg->pending_reception ?? 0),
            'pending_reception_period' => (int) ($woAgg->pending_reception_period ?? 0),
            'spk_periode_non_gr' => $spkPeriodeNonGr,
            'spk_gr_periode' => $spkGrPeriode,
            'spk_tiba_periode' => $spkTibaPeriode,
            'spk_belum_tiba_periode' => $spkPeriodeNonGr - $spkTibaPeriode,
            'spk_rd_periode' => $spkRdPeriode,
            'intake_fisik_berbayar' => $intakeFisik['berbayar'],
            'intake_fisik_garansi' => $intakeFisik['garansi'],
            'intake_fisik_rd' => $intakeFisik['rd'],
            'wip_tahap' => $wip['tahap'],
            'wip_per_status' => $wip['per_status'],
            'siap_kirim_aktif' => (int) ($woAgg->siap_kirim_aktif ?? 0),
            'finished_not_stored' => (int) ($woAgg->finished_not_stored ?? 0),
            'stored_items' => (int) ($rackAgg->stored_items ?? 0),
            'shipping_pending' => Shipping::where('is_verified', false)->count(),
            'ready_for_pickup' => (int) ($woAgg->ready_for_pickup ?? 0),
            'shoes_in_rack_count' => (int) ($saAgg->shoes_in_rack_count ?? 0),
            'donation_candidates_count' => (int) ($saAgg->donation_candidates_count ?? 0),
            
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
            'finished_day' => (int) ($woAgg->finished_day ?? 0),
            'total_incoming' => (int) ($woAgg->total_incoming ?? 0),
            'total_finished' => (int) ($woAgg->finished_day ?? 0),
            'total_qc_processed' => (int) ($woAgg->total_qc_processed ?? 0),
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

        // Audit daily OTW_WORKSHOP & QC_REJECTED in 1 query
        $logsDaily = WorkOrderLog::whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('step', 'OTW_WORKSHOP')->where('action', 'STATUS_CHANGE');
                })->orWhere('action', 'QC_REJECTED');
            })
            ->selectRaw("
                DATE(created_at) as date,
                COUNT(DISTINCT CASE WHEN step = 'OTW_WORKSHOP' AND action = 'STATUS_CHANGE' THEN work_order_id END) as otw_count,
                SUM(CASE WHEN action = 'QC_REJECTED' THEN 1 ELSE 0 END) as reject_count
            ")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

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
            
            $logRow = $logsDaily->get($dateStr);
            $masukCount = $inbound->firstWhere('date', $dateStr)->count ?? 0;
            $otwCount = $logRow ? (int) $logRow->otw_count : 0;
            $rejectCount = $logRow ? (int) $logRow->reject_count : 0;
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
        $trendsRaw = WorkOrder::whereIn('warehouse_qc_status', ['lolos', 'reject'])
            ->whereNotNull('warehouse_qc_at')
            ->whereBetween('warehouse_qc_at', [$start, $end])
            ->selectRaw("
                DATE(warehouse_qc_at) as date,
                SUM(CASE WHEN warehouse_qc_status = 'lolos' THEN 1 ELSE 0 END) as lolos_count,
                SUM(CASE WHEN warehouse_qc_status = 'reject' THEN 1 ELSE 0 END) as reject_count
            ")
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $lolosData = [];
        $rejectData = [];
        
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            
            $row = $trendsRaw->get($dateStr);
            $lolosData[] = $row ? (int) $row->lolos_count : 0;
            $rejectData[] = $row ? (int) $row->reject_count : 0;
            
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
        $byCategory = Material::query()
            ->selectRaw('category, COALESCE(SUM(stock * price), 0) as category_value')
            ->groupBy('category')
            ->pluck('category_value', 'category')
            ->map(fn($val) => (float) $val);

        return [
            'total' => $byCategory->sum(),
            'by_category' => $byCategory,
        ];
    }

    public function getMaterialTrends()
    {
        $topMaterials = Material::orderBy('stock', 'desc')->take(5)->get(['name', 'stock']);

        return [
            'labels' => $topMaterials->pluck('name'),
            'data' => $topMaterials->pluck('stock'),
        ];
    }

    public function getHeatmapData(?\Illuminate\Support\Collection $preloadedRacks = null)
    {
        $racks = $preloadedRacks ?? StorageRack::active()->orderBy('rack_code')->get();

        return $racks->map(function($rack) {
            $usage = ($rack->current_count / max(1, $rack->capacity)) * 100;
            $catVal = $rack->category instanceof \BackedEnum ? $rack->category->value : $rack->category;
            return [
                'code' => $rack->rack_code,
                'usage' => round($usage),
                'color' => $usage > 90 ? 'black' : ($usage > 50 ? 'yellow' : 'green'),
                'count' => $rack->current_count,
                'capacity' => $rack->capacity,
                'category' => $catVal
            ];
        });
    }

    public function getStorageStats(?\Illuminate\Support\Collection $preloadedRacks = null)
    {
        if ($preloadedRacks !== null) {
            $nonAcc = $preloadedRacks->filter(fn($r) => ($r->category instanceof \BackedEnum ? $r->category->value : $r->category) !== \App\Enums\StorageCategory::ACCESSORIES->value);
            $totalCap = (int) $nonAcc->sum('capacity');
            $currUsage = (int) $nonAcc->sum('current_count');
            return [
                'total_capacity' => $totalCap,
                'current_usage' => $currUsage,
                'available_slots' => $totalCap - $currUsage,
            ];
        }

        $row = StorageRack::active()
            ->where('category', '!=', \App\Enums\StorageCategory::ACCESSORIES)
            ->selectRaw('COALESCE(SUM(capacity), 0) as total_capacity, COALESCE(SUM(current_count), 0) as current_usage')
            ->first();

        $totalCap = (int) ($row->total_capacity ?? 0);
        $currUsage = (int) ($row->current_usage ?? 0);

        return [
            'total_capacity' => $totalCap,
            'current_usage' => $currUsage,
            'available_slots' => $totalCap - $currUsage,
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

    /**
     * Get detailed SPK data for Sepatu Masuk (Before)
     */
    public function getSepatuMasukDetail(?Carbon $start = null, ?Carbon $end = null, ?string $search = null)
    {
        return WorkOrder::with(['customer', 'workOrderServices', 'lead'])
            ->whereNotNull('entry_date')
            ->when($start && $end, function($q) use ($start, $end) {
                $q->whereBetween('entry_date', [$start, $end]);
            })
            ->where('status', '!=', WorkOrderStatus::SPK_PENDING)
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('spk_number', 'like', "%{$search}%")
                       ->orWhere('customer_name', 'like', "%{$search}%")
                       ->orWhere('shoe_brand', 'like', "%{$search}%");
                });
            })
            ->orderBy('entry_date', 'desc')
            ->get();
    }

    /**
     * Get detailed SPK data for After Masuk
     */
    public function getAfterMasukDetail(?Carbon $start = null, ?Carbon $end = null, ?string $search = null)
    {
        return WorkOrder::with(['customer', 'workOrderServices', 'storageAssignments' => fn($q) => $q->stored(), 'lead'])
            ->whereNotNull('finished_date')
            ->when($start && $end, function($q) use ($start, $end) {
                $q->whereBetween('finished_date', [$start, $end]);
            })
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('spk_number', 'like', "%{$search}%")
                       ->orWhere('customer_name', 'like', "%{$search}%")
                       ->orWhere('shoe_brand', 'like', "%{$search}%");
                });
            })
            ->orderBy('finished_date', 'desc')
            ->get();
    }
}
