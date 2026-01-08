<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Models\Material;
use App\Models\User;
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
            'preparationProductivity' => $this->getPreparationProductivity(),
            'inventoryValue' => $this->getInventoryValue(),
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
            ->withCount(['jobsProduction' => function($query) {
                $query->where('status', '>=', 'PRODUCTION');
            }])
            ->orderBy('jobs_production_count', 'desc')
            ->take(5)
            ->get();

        return $technicians->map(function($tech) {
            return [
                'name' => $tech->name,
                'count' => $tech->jobs_production_count ?? 0,
            ];
        });
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

        // Daily revenue for last 7 days
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
            'Sortir - Cuci' => collect(),
            'Sortir - Proses Bongkar Sol' => collect(),
            'Sortir - Proses Bongkar Upper' => collect(),
            'QC - Proses Jahit Sol' => collect(),
            'QC - Proses Clean Up' => collect(),
            'QC - Proses QC Akhir' => collect(),
            'Rak Selesai / Pickup Area (Rumah Hijau)' => collect(),
        ];
        
        // 1. Standard Locations (from current_location column)
        $standardOrders = WorkOrder::whereNotNull('current_location')
            ->whereNotIn('status', ['PREPARATION', 'QC']) // Exclude these to avoid double counting if they have stale location text
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

        // 2. Sortir / Preparation Sub-processes
        // Logic: Check logs to see what's done. 
        // Order: Cuci -> Bongkar Sol -> Bongkar Upper.
        // If Cuci NOT done -> Sortir - Cuci
        // If Cuci DONE, Sol needed & NOT done -> Sortir - Bongkar Sol
        // If Cuci DONE, (Sol done OR not needed), Upper needed & NOT done -> Sortir - Bongkar Upper
        // If All done but still in PREPARATION status -> Sortir - Cuci (Fallback)
        
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
                $allLocations['Sortir - Cuci']->push($order);
            } elseif ($hasRepair && !$solDone) {
                $allLocations['Sortir - Proses Bongkar Sol']->push($order);
            } elseif ($hasRepaint && !$upperDone) {
                $allLocations['Sortir - Proses Bongkar Upper']->push($order);
            } else {
                // If everything done or logic ambiguous, maybe waiting for finish?
                // Show in last stage or standard shelf? Let's put in Upper as generic "Finishing Prep"
                $allLocations['Sortir - Proses Bongkar Upper']->push($order);
            }
        }

        // 3. QC Sub-processes
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

    private function getPreparationProductivity()
    {
        // Get preparation technicians performance
        $prepTechs = User::whereIn('role', ['technician'])
            ->withCount([
                'jobsSortirSol as sortir_sol_count',
                'jobsSortirUpper as sortir_upper_count',
            ])
            ->get()
            ->map(function($tech) {
                $totalJobs = ($tech->sortir_sol_count ?? 0) + ($tech->sortir_upper_count ?? 0);
                return [
                    'name' => $tech->name,
                    'total' => $totalJobs,
                    'sol' => $tech->sortir_sol_count ?? 0,
                    'upper' => $tech->sortir_upper_count ?? 0,
                ];
            })
            ->filter(function($tech) {
                return $tech['total'] > 0;
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        // Average preparation time from logs
        $prepOrders = WorkOrder::where('status', '>=', 'PREPARATION')
            ->with(['logs' => function($query) {
                $query->whereIn('step', ['PREPARATION', 'SORTIR'])
                    ->orderBy('created_at');
            }])
            ->get();

        $totalTime = 0;
        $count = 0;
        
        foreach ($prepOrders as $order) {
            $prepStart = $order->logs->firstWhere('step', 'PREPARATION');
            $prepEnd = $order->logs->firstWhere('step', 'SORTIR');
            
            if ($prepStart && $prepEnd) {
                $hours = $prepStart->created_at->diffInHours($prepEnd->created_at);
                $totalTime += $hours;
                $count++;
            }
        }

        $avgPrepTime = $count > 0 ? $totalTime / $count : 0;

        return [
            'technicians' => $prepTechs,
            'avgTime' => round($avgPrepTime, 1),
            'totalOrders' => WorkOrder::where('status', '>=', 'PREPARATION')->count(),
        ];
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
}
