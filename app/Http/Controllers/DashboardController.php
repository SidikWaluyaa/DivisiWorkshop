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

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'statusDistribution' => $this->getStatusDistribution(),
            'dailyTrends' => $this->getDailyTrends(),
            'processingTimes' => $this->getProcessingTimes(),
            'technicianPerformance' => $this->getTechnicianPerformance(),
            'servicePopularity' => $this->getServicePopularity(),
            'revenueData' => $this->getRevenueData(),
            'materialAlerts' => $this->getMaterialAlerts(),
            'upcomingDeadlines' => $this->getUpcomingDeadlines(),
            'locations' => $this->getLocationData(),
            'materialTrends' => $this->getMaterialTrends(),
            'serviceTrends' => $this->getServiceTrends(),
            'processAnalytics' => $this->getProcessAnalytics(),
            'inventoryValue' => $this->getInventoryValue(),
            'purchaseStats' => $this->getPurchaseStats(),
            'supplierAnalytics' => $this->getSupplierAnalytics(),
            'materialCategoryStats' => $this->getMaterialCategoryStats(),
            'technicianSpecializationStats' => $this->getTechnicianSpecializationStats(),
        ];

        return view('dashboard', $data);
    }

    private function getStatusDistribution()
    {
        $statuses = WorkOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_keys($statuses),
            'data' => array_values($statuses),
        ];
    }

    private function getDailyTrends($days = 7)
    {
        $trends = WorkOrder::where('created_at', '>=', Carbon::now()->subDays($days))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing dates with 0
        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');
            $found = $trends->firstWhere('date', $date);
            $data[] = $found ? $found->count : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getProcessingTimes()
    {
        // Calculate average time between status changes
        // This is a simplified version - you might want to use logs for more accuracy
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
            $avg = WorkOrder::where('status', '>=', $key)
                ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at)'));
            $times[$label] = round($avg ?? 0, 1);
        }

        return [
            'labels' => array_keys($times),
            'data' => array_values($times),
        ];
    }

    private function getTechnicianPerformance()
    {
        $technicians = User::whereIn('role', ['technician'])
            ->withCount([
                'jobsPrepWashing', 'jobsPrepSol', 'jobsPrepUpper',
                'jobsProdSol', 'jobsProdUpper', 'jobsProdCleaning',
                'jobsQcJahit', 'jobsQcCleanup', 'jobsQcFinal'
            ])
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

    private function getServicePopularity()
    {
        $services = DB::table('work_order_services')
            ->join('services', 'work_order_services.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('count(*) as count'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('count', 'desc')
            ->get();

        return [
            'labels' => $services->pluck('name')->toArray(),
            'data' => $services->pluck('count')->toArray(),
        ];
    }

    private function getRevenueData()
    {
        // Get completed orders with their service prices
        $completedOrders = WorkOrder::whereIn('status', ['SELESAI'])
            ->with('services')
            ->get();

        $totalRevenue = 0;
        foreach ($completedOrders as $order) {
            $totalRevenue += $order->services->sum('price');
        }

        // Calculate revenue for different periods
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // Today's revenue
        $todayOrders = WorkOrder::whereIn('status', ['SELESAI'])
            ->whereDate('updated_at', $today)
            ->with('services')
            ->get();
        $todayRevenue = $todayOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // This week's revenue
        $weekOrders = WorkOrder::whereIn('status', ['SELESAI'])
            ->whereBetween('updated_at', [$startOfWeek, Carbon::now()])
            ->with('services')
            ->get();
        $weekRevenue = $weekOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // This month's revenue
        $monthOrders = WorkOrder::whereIn('status', ['SELESAI'])
            ->whereBetween('updated_at', [$startOfMonth, Carbon::now()])
            ->with('services')
            ->get();
        $monthRevenue = $monthOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // This year's revenue
        $yearOrders = WorkOrder::whereIn('status', ['SELESAI'])
            ->whereBetween('updated_at', [$startOfYear, Carbon::now()])
            ->with('services')
            ->get();
        $yearRevenue = $yearOrders->sum(function($order) {
            return $order->services->sum('price');
        });

        // Daily revenue for last 7 days (for chart)
        $dailyRevenue = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');
            
            $dayRevenue = WorkOrder::whereIn('status', ['SELESAI'])
                ->whereDate('updated_at', $date)
                ->with('services')
                ->get()
                ->sum(function($order) {
                    return $order->services->sum('price');
                });
            
            $dailyRevenue[] = $dayRevenue;
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

    private function getLocationData()
    {
        // Define all possible locations
        $allLocations = [
            'Gudang Penerimaan' => collect(),
            'Preparation - Cuci' => collect(),
            'Preparation - Proses Bongkar Sol' => collect(),
            'Preparation - Proses Bongkar Upper' => collect(),
            'Sortir - Cek Material' => collect(),
            'QC - Proses Jahit Sol' => collect(),
            'QC - Proses Clean Up' => collect(),
            'QC - Proses QC Akhir' => collect(),
            'Rak Selesai / Pickup Area (Rumah Hijau)' => collect(),
        ];
        
        // 1. Standard Locations (from current_location column)
        $standardOrders = WorkOrder::whereNotNull('current_location')
            ->whereNotIn('status', ['PREPARATION', 'SORTIR', 'QC']) // Exclude these to avoid double counting if they have stale location text
            ->orderBy('updated_at', 'desc')
            ->get();

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
        $prepOrders = WorkOrder::where('status', 'PREPARATION')
            ->with(['services', 'logs'])
            ->orderBy('updated_at', 'desc')
            ->get();

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

    private function getMaterialTrends($days = 7)
    {
        // Get material usage from work_order_materials for last N days
        $trends = DB::table('work_order_materials')
            ->join('materials', 'work_order_materials.material_id', '=', 'materials.id')
            ->join('work_orders', 'work_order_materials.work_order_id', '=', 'work_orders.id')
            ->where('work_orders.created_at', '>=', Carbon::now()->subDays($days))
            ->select('materials.name', DB::raw('SUM(work_order_materials.quantity) as total_used'))
            ->groupBy('materials.id', 'materials.name')
            ->orderBy('total_used', 'desc')
            ->take(5)
            ->get();

        return [
            'labels' => $trends->pluck('name')->toArray(),
            'data' => $trends->pluck('total_used')->toArray(),
        ];
    }

    private function getServiceTrends($days = 7)
    {
        // Get service usage trend over time
        $trends = DB::table('work_order_services')
            ->join('services', 'work_order_services.service_id', '=', 'services.id')
            ->join('work_orders', 'work_order_services.work_order_id', '=', 'work_orders.id')
            ->where('work_orders.created_at', '>=', Carbon::now()->subDays($days))
            ->select(DB::raw('DATE(work_orders.created_at) as date'), 'services.name', DB::raw('count(*) as count'))
            ->groupBy('date', 'services.id', 'services.name')
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
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = Carbon::now()->subDays($i)->format('d M');
        }

        $datasets = [];
        foreach ($topServices as $service) {
            $data = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $found = $trends->where('date', $date)->where('name', $service->name)->first();
                $data[] = $found ? $found->count : 0;
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

    private function getProcessAnalytics()
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

    private function getPurchaseStats()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Pending POs count
        $pendingPOs = Purchase::where('status', 'pending')->count();

        // Outstanding Debt (Unpaid + Partial remaining)
        // Note: Logic allows checking 'payment_status' or calculation. 
        // Using calculation for accuracy on partials.
        $outstandingDebt = Purchase::whereIn('payment_status', ['unpaid', 'partial'])
            ->get()
            ->sum(function($p) {
                return $p->total_price - $p->paid_amount;
            });

        // Monthly Spend (Total of POs created this month)
        $monthlySpend = Purchase::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        return [
            'pending_po' => $pendingPOs,
            'outstanding_debt' => $outstandingDebt,
            'monthly_spend' => $monthlySpend,
        ];
    }

    private function getSupplierAnalytics()
    {
        // Top Suppliers by Spend (Volume)
        $topBySpend = Purchase::whereNotNull('supplier_name')
            ->select('supplier_name', DB::raw('SUM(total_price) as total_spend'))
            ->groupBy('supplier_name')
            ->orderByDesc('total_spend')
            ->take(5)
            ->get();

        // Top Suppliers by Rating (Quality)
        $topByRating = Purchase::whereNotNull('supplier_name')
            ->whereNotNull('quality_rating')
            ->select('supplier_name', DB::raw('AVG(quality_rating) as avg_rating'), DB::raw('COUNT(*) as count'))
            ->groupBy('supplier_name')
            ->having('count', '>=', 1) // At least 1 rated order
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
        $stats = Material::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
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
