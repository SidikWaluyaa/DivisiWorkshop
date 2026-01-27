<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Models\User;
use App\Models\Service; // Added to fix lint error
use App\Models\Purchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Complaint;
use App\Models\StorageAssignment;
use App\Enums\WorkOrderStatus;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $filter = $request->input('period', '30d');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');
        
        // Default range
        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay();
        $periodLabel = '30 Hari Terakhir';

        // Preset logic
        if ($filter === 'today') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $periodLabel = 'Hari Ini';
        } elseif ($filter === '7d') {
            $startDate = now()->subDays(7)->startOfDay();
            $endDate = now()->endOfDay();
            $periodLabel = '7 Hari Terakhir';
        } elseif ($filter === 'this_month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $periodLabel = now()->format('F Y');
        } elseif ($filter === 'last_month') {
            $startDate = now()->subMonth()->startOfMonth();
            $endDate = now()->subMonth()->endOfMonth();
            $periodLabel = now()->subMonth()->format('F Y');
        } elseif ($filter === 'ytd') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfDay();
            $periodLabel = 'Year to Date (' . now()->year . ')';
        } elseif ($filter === 'custom' && $customStart && $customEnd) {
            $startDate = Carbon::parse($customStart)->startOfDay();
            $endDate = Carbon::parse($customEnd)->endOfDay();
            $periodLabel = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
        } elseif ($request->has('month')) {
            // Backward compatibility for old month/year filters
            $month = $request->input('month');
            $startDate = Carbon::parse($month . '-01')->startOfMonth();
            $endDate = Carbon::parse($month . '-01')->endOfMonth();
            $periodLabel = Carbon::parse($month)->format('F Y');
            $filter = 'custom';
        }
        
        $data = [
            'statusDistribution' => $this->getStatusDistribution($startDate, $endDate),
            'dailyTrends' => $this->getDailyTrends($startDate, $endDate),
            'processingTimes' => $this->getProcessingTimes($startDate, $endDate),
            'technicianPerformance' => $this->getTechnicianPerformance($startDate, $endDate),
            'servicePopularity' => $this->getServicePopularity($startDate, $endDate),
            'revenueData' => $this->getRevenueData($startDate, $endDate),
            'materialAlerts' => $this->getMaterialAlerts(), // Inventory doesn't depend on date range usually, checking below
            'upcomingDeadlines' => $this->getUpcomingDeadlines(), // Future dates
            'locations' => $this->getLocationData($startDate, $endDate),
            'materialTrends' => $this->getMaterialTrends($startDate, $endDate),
            'serviceTrends' => $this->getServiceTrends($startDate, $endDate),
            'processAnalytics' => $this->getProcessAnalytics($startDate, $endDate),
            'inventoryValue' => $this->getInventoryValue(), // Snapshot
            'purchaseStats' => $this->getPurchaseStats($startDate, $endDate),
            'supplierAnalytics' => $this->getSupplierAnalytics($startDate, $endDate),
            'materialCategoryStats' => $this->getMaterialCategoryStats(), // Snapshot
            'technicianSpecializationStats' => $this->getTechnicianSpecializationStats(), // Snapshot of user base
            'complaintAnalytics' => $this->getComplaintAnalytics($startDate, $endDate),
            'financialMetrics' => $this->getFinancialMetrics($startDate, $endDate),
            'customerRetention' => $this->getCustomerRetention($startDate, $endDate),
            
            'activeOrdersCount' => WorkOrder::whereNotIn('status', ['SELESAI', 'DIBATALKAN'])->count(),
            'activeStaffCount' => User::count(), // Simple count for now, can be refined later
            
            // Warehouse Dashboard Stats for Quick Access
            'warehouseStats' => [
                'pending_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)->count(),
                'stored_items' => StorageAssignment::stored()->count(),
                'ready_for_pickup' => WorkOrder::where('status', WorkOrderStatus::SELESAI)->whereNull('taken_date')->count(),
            ],
            
            // Filter metadata
            'selectedPeriod' => $filter,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'periodLabel' => $periodLabel,
        ];

        return view('dashboard', $data);
    }

    private function getStatusDistribution($startDate = null, $endDate = null)
    {
        $query = WorkOrder::select('status', DB::raw('count(*) as count'));
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
            
        $statuses = $query->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_keys($statuses),
            'data' => array_values($statuses),
        ];
    }

    private function getDailyTrends($startDate = null, $endDate = null)
    {
        // Default to last 7 days if no dates provided
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->subDays(7);
            $endDate = Carbon::now();
        }

        $trends = WorkOrder::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with 0
        $labels = [];
        $data = [];
        
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            $found = $trends->firstWhere('date', $dateStr);
            $data[] = $found ? $found->count : 0;
            $current->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getProcessingTimes($startDate = null, $endDate = null)
    {
        // Calculate average time between status changes
        $stages = [
            'ASSESSMENT' => 'Assessment',
            'PREPARATION' => 'Preparation',
            'SORTIR' => 'Sortir',
            'PRODUCTION' => 'Production',
            'QC' => 'QC',
        ];

        $times = [];
        foreach ($stages as $key => $label) {
            // Get average hours for orders that passed through this stage
            $query = WorkOrder::where('status', '>=', $key);
            
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
                
            $avg = $query->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at)'));
            $times[$label] = round($avg ?? 0, 1);
        }

        return [
            'labels' => array_keys($times),
            'data' => array_values($times),
        ];
    }

    private function getTechnicianPerformance($startDate = null, $endDate = null)
    {
        // Include both technician and pic roles for comprehensive performance tracking
        $technicians = User::whereIn('role', ['technician', 'pic'])
            // Ideally should use a more dynamic way if possible, but keeping current structure
            // NOTE: withCount doesn't easily support dynamic date filtering on relationships unless using closure
            // For now, let's keep it simple or if needed, we might need to change how we fetch this
            // Simplification: We will filter the final mapping or just accept that this count is all-time for now
            // But user asked for dashboard filter.
            
            // Let's try to filter via closure if these are relationships
            // Assuming jobsPrepWashing etc are hasMany relationships
            
            ->withCount(['jobsPrepWashing' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsPrepSol' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsPrepUpper' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsProdSol' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsProdUpper' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsProdCleaning' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsQcJahit' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsQcCleanup' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['jobsQcFinal' => function($q) use ($startDate, $endDate) {
                if($startDate && $endDate) $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($tech) {
                // Calculate total weighted performance
                $total = 
                    ($tech->jobs_prep_washing_count ?? 0) +
                    ($tech->jobs_prep_sol_count ?? 0) +
                    ($tech->jobs_prep_upper_count ?? 0) +
                    ($tech->jobs_prod_sol_count ?? 0) +
                    ($tech->jobs_prod_upper_count ?? 0) +
                    ($tech->jobs_prod_cleaning_count ?? 0) +
                    ($tech->jobs_qc_jahit_count ?? 0) +
                    ($tech->jobs_qc_cleanup_count ?? 0) +
                    ($tech->jobs_qc_final_count ?? 0);
                    
                return [
                    'name' => $tech->name,
                    'count' => $total,
                    'specialization' => $tech->specialization ?? 'Unassigned',
                ];
            })
            ->sortByDesc('count')
            ->groupBy('specialization');

        return $technicians;
    }

    private function getServicePopularity($startDate = null, $endDate = null)
    {
        $query = DB::table('work_order_services')
            ->join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id') // Need to join WO to get date
            ->join('services', 'work_order_services.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(*) as count'));
            
        if ($startDate && $endDate) {
            $query->whereBetween('work_orders.created_at', [$startDate, $endDate]);
        }
            
        $services = $query->groupBy('services.id', 'services.name')
            ->orderByRaw('COUNT(*) desc')
            ->get();

        return [
            'labels' => $services->pluck('name')->toArray(),
            'data' => $services->pluck('count')->toArray(),
        ];
    }

    private function getRevenueData($startDate = null, $endDate = null)
    {
        // Get completed orders with their service prices (SELESAI and TERKIRIM) based on filter
        $query = WorkOrder::whereIn('status', ['SELESAI', 'TERKIRIM']);
        
        if ($startDate && $endDate) {
            $query->whereBetween('updated_at', [$startDate, $endDate]);
        }
        
        $completedOrders = $query->with('services')->get();

        $totalRevenue = 0;
        foreach ($completedOrders as $order) {
            $totalRevenue += $order->services->sum('price');
        }

        // Calculate revenue for different periods (Live Snapshots - unaffected by filter)
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // Today's revenue
        $todayOrders = WorkOrder::whereIn('status', ['SELESAI', 'TERKIRIM'])
            ->whereDate('updated_at', $today)
            ->with('services')
            ->get();
        $todayRevenue = $todayOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // This week's revenue
        $weekOrders = WorkOrder::whereIn('status', ['SELESAI', 'TERKIRIM'])
            ->whereBetween('updated_at', [$startOfWeek, Carbon::now()])
            ->with('services')
            ->get();
        $weekRevenue = $weekOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // This month's revenue
        $monthOrders = WorkOrder::whereIn('status', ['SELESAI', 'TERKIRIM'])
            ->whereBetween('updated_at', [$startOfMonth, Carbon::now()])
            ->with('services')
            ->get();
        $monthRevenue = $monthOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // This year's revenue
        $yearOrders = WorkOrder::whereIn('status', ['SELESAI', 'TERKIRIM'])
            ->whereBetween('updated_at', [$startOfYear, Carbon::now()])
            ->with('services')
            ->get();
        $yearRevenue = $yearOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // Chart Data Generation
        $dailyRevenue = [];
        $labels = [];
        
        $chartStart = $startDate ?? Carbon::now()->subDays(7);
        $chartEnd = $endDate ?? Carbon::now();
        
        // Check if duration is > 60 days (implies yearly view or long range) to switch to Monthly view
        $diffInDays = $chartStart->diffInDays($chartEnd);
        
        if ($diffInDays > 60) {
            // MONTHLY AGGREGATION (for Year Filter)
            $current = $chartStart->copy()->startOfMonth();
            // Loop until end of the year or end date
            while ($current <= $chartEnd) {
                // Use English format for code consistency, or ID if localized
                $labels[] = $current->format('M Y'); 
                $monthStr = $current->format('Y-m');
                
                $monthVal = $completedOrders->filter(function($order) use ($monthStr) {
                    return $order->updated_at->format('Y-m') === $monthStr;
                    return $order->services->sum('pivot.cost');
                });
                
                $dailyRevenue[] = $monthVal;
                $current->addMonth();
            }
        } else {
            // DAILY AGGREGATION (for Month Filter / Default)
            $current = $chartStart->copy();
            while ($current <= $chartEnd) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('d M');
                
                $dayRevenue = $completedOrders->filter(function($order) use ($dateStr) {
                    return $order->updated_at->format('Y-m-d') === $dateStr;
                    return $order->services->sum('pivot.cost');
                });
                
                $dailyRevenue[] = $dayRevenue;
                $current->addDay();
            }
        }

        return [
            'total' => $totalRevenue,
            'periods' => [
                'today' => [
                    'total' => $todayRevenue,
                    'count' => $todayOrders->count()
                ],
                'week' => [
                    'total' => $weekRevenue,
                    'count' => $weekOrders->count()
                ],
                'month' => [
                    'total' => $monthRevenue,
                    'count' => $monthOrders->count()
                ],
                'year' => [
                    'total' => $yearRevenue,
                    'count' => $yearOrders->count()
                ],
            ],
            'daily' => [
                'labels' => $labels,
                'data' => $dailyRevenue,
            ],
        ];
    }

    private function getFinancialMetrics($startDate = null, $endDate = null)
    {
        $orders = WorkOrder::whereNotIn('status', ['DIBATALKAN'])
            ->with(['services', 'materials'])
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();
            
        $revenue = $orders->sum(function($order) {
            return $order->services->sum('pivot.cost');
        });
        
        $materialCost = $orders->sum(function($order) {
            return $order->materials->sum(function($m) {
                return $m->pivot->quantity * $m->price;
            });
        });
        
        $netProfit = $revenue - $materialCost;
        $margin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
        
        return [
            'revenue' => $revenue,
            'material_cost' => $materialCost,
            'net_profit' => $netProfit,
            'margin' => round($margin, 1),
        ];
    }

    private function getCustomerRetention($startDate = null, $endDate = null)
    {
        $periodCustomersQuery = WorkOrder::select('customer_phone')
            ->when($startDate && $endDate, function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->distinct();

        $periodCustomers = $periodCustomersQuery->pluck('customer_phone');
            
        if ($periodCustomers->isEmpty()) {
            return ['rate' => 0, 'new' => 0, 'returning' => 0, 'total' => 0];
        }
        
        $ids = $periodCustomers->toArray();
        
        // Count how many orders each of these customers has in TOTAL (Lifetime)
        $counts = WorkOrder::whereIn('customer_phone', $ids)
            ->select('customer_phone', DB::raw('count(*) as total'))
            ->groupBy('customer_phone')
            ->pluck('total', 'customer_phone');
            
        $returning = $counts->filter(fn($c) => $c > 1)->count();
        $total = $periodCustomers->count();
        
        return [
            'rate' => $total > 0 ? round(($returning / $total) * 100, 1) : 0,
            // New customers in this context are those with exactly 1 order (First time) or 
            // strictly those whose First Order Date is in this period. 
            // This logic assumes "Returning" = >1 lifetime order.
            'new' => $total - $returning, 
            'returning' => $returning,
            'total' => $total
        ];
    }

    private function getMaterialAlerts()
    {
        return Material::whereRaw('stock <= min_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();
    }

    private function getUpcomingDeadlines()
    {
        $today = Carbon::today();
        
        return [
            'today' => WorkOrder::whereDate('estimation_date', $today)
                ->whereNotIn('status', ['SELESAI'])
                ->count(),
            'tomorrow' => WorkOrder::whereDate('estimation_date', $today->copy()->addDay())
                ->whereNotIn('status', ['SELESAI'])
                ->count(),
            'thisWeek' => WorkOrder::whereBetween('estimation_date', [$today, $today->copy()->addDays(7)])
                ->whereNotIn('status', ['SELESAI'])
                ->count(),
        ];
    }

    private function getLocationData($startDate = null, $endDate = null)
    {
        // Define all possible locations
        $allLocations = [
            'Gudang Penerimaan' => collect(),
            'Preparation - Cuci' => collect(),
            'Preparation - Proses Bongkar Sol' => collect(),
            'Preparation - Proses Bongkar Upper' => collect(),
            'Sortir - Cek Material' => collect(),
            'Production - Dalam Pengerjaan' => collect(),
            'QC - Proses Jahit Sol' => collect(),
            'QC - Proses Clean Up' => collect(),
            'QC - Proses QC Akhir' => collect(),
            'Rak Selesai / Pickup Area (Rumah Hijau)' => collect(),
        ];
        
        // 1. Standard Locations (from current_location column)
        $query = WorkOrder::whereNotNull('current_location')
            ->whereNotIn('status', ['PREPARATION', 'SORTIR', 'QC']); // Exclude these to avoid double counting if they have stale location text
            
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
            
        $standardOrders = $query->orderBy('updated_at', 'desc')->get();

        foreach ($standardOrders as $order) {
            $loc = $order->current_location;
            if (isset($allLocations[$loc])) {
                $allLocations[$loc]->push($order);
            } else {
                // Determine fallback based on status if location name doesn't match
                if ($order->status == 'DITERIMA') $allLocations['Gudang Penerimaan']->push($order);
                elseif ($order->status == 'SELESAI') $allLocations['Rak Selesai / Pickup Area (Rumah Hijau)']->push($order);
            }
        }

        // 2. Preparation Sub-processes (Cuci, Bongkar Sol, Bongkar Upper)
        // Logic: Check logs to see what's done. 
        $queryPrep = WorkOrder::where('status', 'PREPARATION')
            ->with(['services', 'logs']);
        
        if ($startDate && $endDate) {
            $queryPrep->whereBetween('created_at', [$startDate, $endDate]);
        }
            
        $prepOrders = $queryPrep->orderBy('updated_at', 'desc')->get();

        foreach ($prepOrders as $order) {
            $logs = $order->logs->pluck('action')->toArray();
            $services = $order->services;
            
            $hasRepair = $services->contains(fn($s) => in_array($s->category, ['Reparasi Sol']));
            $hasRepaint = $services->contains(fn($s) => in_array($s->category, ['Reparasi Upper', 'Repaint']));
            
            $cuciDone = in_array('PREP_CLEANING_DONE', $logs);
            $solDone = in_array('PREP_SOL_DONE', $logs);
            $upperDone = in_array('PREP_UPPER_DONE', $logs);
            
            if (!$cuciDone) {
                $allLocations['Preparation - Cuci']->push($order);
            } elseif ($hasRepair && !$solDone) {
                $allLocations['Preparation - Proses Bongkar Sol']->push($order);
            } elseif ($hasRepaint && !$upperDone) {
                $allLocations['Preparation - Proses Bongkar Upper']->push($order);
            } else {
                $allLocations['Preparation - Proses Bongkar Upper']->push($order);
            }
        }
        
        // 3. Sortir (Material Check)
        $sortirOrders = WorkOrder::where('status', 'SORTIR')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($sortirOrders as $order) {
            $allLocations['Sortir - Cek Material']->push($order);
        }

        // 3.5. Production
        $productionOrders = WorkOrder::where('status', 'PRODUCTION')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($productionOrders as $order) {
            $allLocations['Production - Dalam Pengerjaan']->push($order);
        }

        // 4. QC Sub-processes
        // Order: Jahit Sol -> Clean Up -> QC Akhir
        // Logic similar to above.
        
        $qcOrders = WorkOrder::where('status', 'QC')
            ->with(['logs']) // Services maybe needed if Jahit is conditional? Assuming Jahit always checked in QC phase for now or based on log existence
            ->orderBy('updated_at', 'desc')
            ->get();
            
        foreach ($qcOrders as $order) {
            $logs = $order->logs->pluck('action')->toArray();
            
            $jahitDone = in_array('QC_JAHIT_DONE', $logs);
            $cleanupDone = in_array('QC_CLEANUP_DONE', $logs);
            // Final check is the last step, so if CleanUp done, it's in Final QC
            
            if (!$jahitDone) {
                $allLocations['QC - Proses Jahit Sol']->push($order);
            } elseif (!$cleanupDone) {
                 $allLocations['QC - Proses Clean Up']->push($order);
            } else {
                 $allLocations['QC - Proses QC Akhir']->push($order);
            }
        }
        
        return collect($allLocations);
    }

    private function getMaterialTrends($startDate = null, $endDate = null)
    {
        // Get material usage from work_order_materials for filtered period
        $query = DB::table('work_order_materials')
            ->join('materials', 'work_order_materials.material_id', '=', 'materials.id')
            ->join('work_orders', 'work_order_materials.work_order_id', '=', 'work_orders.id')
            ->select('materials.name', DB::raw('SUM(work_order_materials.quantity) as total_used'));
            
        if ($startDate && $endDate) {
            $query->whereBetween('work_orders.created_at', [$startDate, $endDate]);
        }
            
        $trends = $query->groupBy('materials.id', 'materials.name')
            ->orderBy('total_used', 'desc')
            ->take(5)
            ->get();

        return [
            'labels' => $trends->pluck('name')->toArray(),
            'data' => $trends->pluck('total_used')->toArray(),
        ];
    }

    private function getServiceTrends($startDate = null, $endDate = null)
    {
        // Get service usage trend over time
        $query = DB::table('work_order_services')
            ->join('services', 'work_order_services.service_id', '=', 'services.id')
            ->join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
            ->select(DB::raw('DATE(work_orders.created_at) as date'), 'services.name', DB::raw('count(*) as count'));
            
        if ($startDate && $endDate) {
            $query->whereBetween('work_orders.created_at', [$startDate, $endDate]);
        } else {
            // Default fallback
            $startDate = now()->subDays(7);
            $endDate = now();
            $query->whereBetween('work_orders.created_at', [$startDate, $endDate]);
        }
            
        $trends = $query->groupBy('date', 'services.id', 'services.name')
            ->orderBy('date')
            ->get();

        // Get top 3 services
        $topServices = DB::table('work_order_services')
            ->join('services', 'work_order_services.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(*) as count'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('count', 'desc')
            ->take(3)
            ->get();

        $labels = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $labels[] = $current->format('d M');
            $current->addDay();
        }

        $datasets = [];
        foreach ($topServices as $service) {
            $data = [];
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dateStr = $current->format('Y-m-d');
                $found = $trends->where('date', $dateStr)->where('name', $service->name)->first();
                $data[] = $found ? $found->count : 0;
                $current->addDay();
            }
            $datasets[] = [
                'label' => $service->name,
                'data' => $data,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private function getProcessAnalytics($startDate = null, $endDate = null)
    {
        // 1. Define Static Stages
        $stages = collect([
            'PREPARATION' => [
                'label' => 'Preparation',
                'log_start' => 'PREPARATION',
                'log_end' => 'SORTIR',
                'tech_relation' => [], // Prep generic
            ],
            'SORTIR' => [
                'label' => 'Sortir',
                'log_start' => 'SORTIR',
                'log_end' => 'PRODUCTION',
                'tech_relation' => ['jobsSortirSol', 'jobsSortirUpper'],
            ],
        ]);

        // 2. Add Dynamic Production Stages (by Service Category)
        // Only take categories that actually have active orders or exist in master data
        $categories = Service::distinct()->pluck('category')->filter();
        
        foreach ($categories as $cat) {
            $stages->put('PROD: ' . $cat, [
                'label' => $cat,
                'log_start' => 'PRODUCTION',
                'log_end' => 'QC',
                'tech_relation' => ['jobsProduction'],
                'filter_category' => $cat
            ]);
        }

        // 3. Add QC Stage
        $stages->put('QC', [
            'label' => 'Quality Control',
            'log_start' => 'QC',
            'log_end' => 'SELESAI',
            'tech_relation' => ['jobsQcJahit', 'jobsQcCleanup', 'jobsQcFinal'],
        ]);

        $analytics = collect();

        foreach ($stages as $key => $config) {
            // QUERY BUILDER for Orders
            // Handle key prefix for PROD
            $statusCheck = str_starts_with($key, 'PROD:') ? 'PRODUCTION' : $key;

            $ordersQuery = WorkOrder::where('status', '>=', $statusCheck);
            
            // Apply Date Filter
            if ($startDate && $endDate) {
                $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            
            // Apply Category Filter if exists
            if (isset($config['filter_category'])) {
                $ordersQuery->whereHas('services', function($q) use ($config) {
                    $q->where('category', $config['filter_category']);
                });
            }

            // A. Calculate Stats (Total & Avg Time)
            // Clone query for stats to avoid mutation issues if any
            $countQuery = clone $ordersQuery;
            $totalOrders = $countQuery->count();

            // Avg Time Calculation
            $ordersForTime = $ordersQuery->with(['logs' => function($query) use ($config) {
                    $query->whereIn('step', [$config['log_start'], $config['log_end']]);
                }])->get(); // Note: Getting all records might be heavy, optimize in future to aggregate or limit

            $totalTime = 0;
            $countTime = 0;
            foreach ($ordersForTime as $order) {
                $start = $order->logs->firstWhere('step', $config['log_start']);
                $end = $order->logs->firstWhere('step', $config['log_end']);
                
                if ($start && $end) {
                    $totalTime += $start->created_at->diffInHours($end->created_at);
                    $countTime++;
                }
            }
            $avgTime = $countTime > 0 ? round($totalTime / $countTime, 1) : 0;

            // C. Calculate On-Time Completion Rate
            $completedQuery = clone $ordersQuery;
            if (isset($config['filter_category'])) {
                $completedQuery->whereHas('services', function($q) use ($config) {
                    $q->where('category', $config['filter_category']);
                });
            }
            $completedOrders = $completedQuery->where('status', 'SELESAI')->get();
            $onTimeCount = $completedOrders->filter(function($order) {
                return $order->estimation_date && $order->updated_at->lte($order->estimation_date);
            })->count();
            $onTimeRate = $completedOrders->count() > 0 ? round(($onTimeCount / $completedOrders->count()) * 100) : 0;

            // D. Calculate Revenue
            $revenueQuery = clone $ordersQuery;
            if (isset($config['filter_category'])) {
                $revenueQuery->whereHas('services', function($q) use ($config) {
                    $q->where('category', $config['filter_category']);
                });
            }
            $revenueOrders = $revenueQuery->where('status', 'SELESAI')->with('services')->get();
            $totalRevenue = 0;
            foreach ($revenueOrders as $order) {
                $services = $order->services;
                if (isset($config['filter_category'])) {
                    $services = $services->where('category', $config['filter_category']);
                }
                $totalRevenue += $services->sum('price');
            }

            // E. Calculate Status Breakdown
            $completedCount = $completedOrders->count();
            $inProgressCount = $totalOrders - $completedCount;

            // B. Calculate Technician Leaderboard with Avg Time
            // We need to count jobs for each technician matching this specific stage/category criteria
            $techs = User::whereIn('role', ['technician'])
                ->get()
                ->map(function($user) use ($config, $statusCheck) {
                    $jobCount = 0;
                    $totalTechTime = 0;
                    $countTechTime = 0;

                    foreach ($config['tech_relation'] as $relation) {
                        // Determine the FK column name from relationship name (naive assumption or mapping)
                        // jobsProduction -> technician_production_id
                        // jobsSortirSol -> pic_sortir_sol_id
                        // jobsQcJahit -> qc_jahit_technician_id
                        $fkColumn = match($relation) {
                            'jobsProduction' => 'technician_production_id',
                            'jobsSortirSol' => 'pic_sortir_sol_id',
                            'jobsSortirUpper' => 'pic_sortir_upper_id',
                            'jobsQcJahit' => 'qc_jahit_technician_id',
                            'jobsQcCleanup' => 'qc_cleanup_technician_id',
                            'jobsQcFinal' => 'qc_final_pic_id',
                            default => null
                        };

                        if ($fkColumn) {
                            $query = WorkOrder::where($fkColumn, $user->id)
                                ->where('status', '>=', $statusCheck)
                                ->with(['logs' => function($query) use ($config) {
                                    $query->whereIn('step', [$config['log_start'], $config['log_end']]);
                                }]);

                            if (isset($config['filter_category'])) {
                                $query->whereHas('services', function($q) use ($config) {
                                    $q->where('category', $config['filter_category']);
                                });
                            }
                            
                            $techOrders = $query->get();
                            $jobCount += $techOrders->count();

                            // Calculate avg time for this tech
                            foreach ($techOrders as $order) {
                                $start = $order->logs->firstWhere('step', $config['log_start']);
                                $end = $order->logs->firstWhere('step', $config['log_end']);
                                
                                if ($start && $end) {
                                    $totalTechTime += $start->created_at->diffInHours($end->created_at);
                                    $countTechTime++;
                                }
                            }
                        }
                    }

                    $avgTechTime = $countTechTime > 0 ? round($totalTechTime / $countTechTime, 1) : 0;

                    return [
                        'name' => $user->name,
                        'count' => $jobCount,
                        'avgTime' => $avgTechTime,
                        'role' => $user->specialization ?? $user->role
                    ];
                })
                ->filter(fn($u) => $u['count'] > 0)
                ->sortByDesc('count')
                ->values()
                ->take(5);

            $analytics->put($config['label'], [
                'totalOrders' => $totalOrders,
                'avgTime' => $avgTime,
                'onTimeRate' => $onTimeRate,
                'revenue' => $totalRevenue,
                'completedCount' => $completedCount,
                'inProgressCount' => $inProgressCount,
                'technicians' => $techs
            ]);
        }
        
        return $analytics;
    }

    private function getInventoryValue()
    {
        $materials = Material::all();
        
        $totalValue = $materials->sum(function($material) {
            return $material->stock * $material->price;
        });

        $byMaterial = $materials->sortByDesc(function($material) {
            return $material->stock * $material->price;
        })->take(5)->map(function($material) {
            return [
                'name' => $material->name,
                'stock' => $material->stock,
                'price' => $material->price,
                'value' => $material->stock * $material->price,
            ];
        })->values();

        return [
            'total' => $totalValue,
            'byMaterial' => $byMaterial,
        ];
    }

    private function getPurchaseStats($startDate = null, $endDate = null)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Base query
        $query = Purchase::query();

        // For counts, if filter applied, we only count POs created in that period
        $pendingQuery = clone $query;
        $debtQuery = clone $query;
        $spendQuery = clone $query;

        if ($startDate && $endDate) {
            $pendingQuery->whereBetween('created_at', [$startDate, $endDate]);
            $debtQuery->whereBetween('created_at', [$startDate, $endDate]);
            $spendQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Pending POs count
        $pendingPOs = $pendingQuery->where('status', 'pending')->count();

        // Outstanding Debt (Unpaid + Partial remaining)
        $outstandingDebt = $debtQuery->whereIn('payment_status', ['unpaid', 'partial'])
            ->get()
            ->sum(function($p) {
                return $p->total_price - $p->paid_amount;
            });

        // Monthly Spend (Total of POs created this month or selected period)
        if ($startDate && $endDate) {
             // If filter is active, use the filter period spend
             $monthlySpend = $spendQuery->sum('total_price');
        } else {
             // Default to this month if no filter
             $monthlySpend = Purchase::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total_price');
        }

        return [
            'pending_po' => $pendingPOs,
            'outstanding_debt' => $outstandingDebt,
            'monthly_spend' => $monthlySpend,
        ];
    }

    private function getComplaintAnalytics($startDate = null, $endDate = null)
    {
        $query = Complaint::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $complaints = $query->get();

        // Status Counts
        $statusCounts = [
            'PENDING' => $complaints->where('status', 'PENDING')->count(),
            'PROCESS' => $complaints->where('status', 'PROCESS')->count(),
            'RESOLVED' => $complaints->where('status', 'RESOLVED')->count(),
            'REJECTED' => $complaints->where('status', 'REJECTED')->count(),
        ];

        // Category Breakdown
        $categoryCounts = $complaints->groupBy('category')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->toArray();

        // Recent Complaints (Limit 5)
        $recentComplaints = Complaint::with('workOrder')
            ->latest()
            ->take(5)
            ->get();

        // Overdue Complaints (Pending > 48h)
        $overdueCount = Complaint::where('status', 'PENDING')
            ->where('created_at', '<', now()->subHours(48))
            ->count();

        return [
            'total' => $complaints->count(),
            'status_counts' => $statusCounts,
            'overdue_count' => $overdueCount,
            'category_counts' => [
                'labels' => array_keys($categoryCounts),
                'data' => array_values($categoryCounts),
            ],
            'recent' => $recentComplaints
        ];
    }

    private function getSupplierAnalytics($startDate = null, $endDate = null)
    {
        // Top Suppliers by Spend (Volume)
        $spendQuery = Purchase::whereNotNull('supplier_name');
        
        if ($startDate && $endDate) {
            $spendQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $topBySpend = $spendQuery->select('supplier_name', DB::raw('SUM(total_price) as total_spend'))
            ->groupBy('supplier_name')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        // Top Suppliers by Rating (Quality)
        $ratingQuery = Purchase::whereNotNull('supplier_name')
            ->whereNotNull('quality_rating');
            
        if ($startDate && $endDate) {
            $ratingQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $topByRating = $ratingQuery->select('supplier_name', DB::raw('AVG(quality_rating) as avg_rating'), DB::raw('COUNT(*) as count_orders'))
            ->groupBy('supplier_name')
            ->havingRaw('COUNT(*) >= 1') // At least 1 rated order
            ->orderByDesc('avg_rating')
            ->take(5)
            ->get();

        return [
            'bySpend' => [
                'labels' => $topBySpend->pluck('supplier_name')->toArray(),
                'data' => $topBySpend->pluck('total_spend')->toArray(),
            ],
            'byRating' => [
                'labels' => $topByRating->pluck('supplier_name')->toArray(),
                'data' => $topByRating->map(fn($item) => round($item->avg_rating, 1))->toArray(),
            ]
        ];
    }
    private function getMaterialCategoryStats()
    {
        $stats = Material::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return [
            'labels' => array_keys($stats),
            'data' => array_values($stats),
        ];
    }

    private function getTechnicianSpecializationStats()
    {
        $stats = User::where('role', 'technician')
            ->select('specialization', DB::raw('count(*) as count'))
            ->groupBy('specialization')
            ->orderBy('count', 'desc')
            ->pluck('count', 'specialization')
            ->toArray();

        return [
            'labels' => array_keys($stats),
            'data' => array_values($stats),
        ];
    }
}
