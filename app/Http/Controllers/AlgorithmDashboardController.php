<?php

namespace App\Http\Controllers;

use App\Services\AlgorithmManagement\AutoAssignmentService;
use App\Services\AlgorithmManagement\LoadBalancingService;
use App\Services\AlgorithmManagement\PriorityCalculationService;
use App\Models\AlgorithmConfig;
use App\Models\AlgorithmLog;
use App\Models\AlgorithmMetric;
use Illuminate\Http\Request;

class AlgorithmDashboardController extends Controller
{
    /**
     * Display the algorithm management dashboard
     */
    public function index()
    {
        // Get all algorithm configs
        $algorithms = AlgorithmConfig::all()->keyBy('algorithm_name');

        // Get services
        $loadBalancing = new LoadBalancingService();
        $loadAnalysis = $loadBalancing->analyze();

        // Get priority distribution
        $priorityService = new PriorityCalculationService();
        $priorityDistribution = $priorityService->getDistribution();

        // Get recent activity logs
        $recentLogs = AlgorithmLog::with(['workOrder', 'user'])
            ->latest()
            ->take(20)
            ->get();

        // Get metrics for each algorithm
        $metrics = [];
        foreach (['auto_assignment', 'load_balancing', 'priority_calculation'] as $algoName) {
            $metrics[$algoName] = [
                'success_rate' => AlgorithmMetric::getLatest($algoName, 'assignment_success_rate') 
                    ?? AlgorithmMetric::getLatest($algoName, 'avg_priority_score') 
                    ?? 0,
                'execution_time' => AlgorithmMetric::getLatest($algoName, 'execution_time') ?? 0,
                'last_run' => $algorithms[$algoName]->last_run_at ?? null,
            ];
        }

        // Calculate overall health
        $overallHealth = $this->calculateOverallHealth($algorithms, $loadAnalysis);

        // Get automation rate (percentage of automated actions)
        $automationRate = $this->calculateAutomationRate();

        return view('algorithm.dashboard.index', compact(
            'algorithms',
            'loadAnalysis',
            'priorityDistribution',
            'recentLogs',
            'metrics',
            'overallHealth',
            'automationRate'
        ));
    }

    /**
     * Toggle algorithm active status
     */
    public function toggleAlgorithm(Request $request, string $algorithmName)
    {
        $config = AlgorithmConfig::where('algorithm_name', $algorithmName)->firstOrFail();
        $config->update(['is_active' => !$config->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $config->is_active,
            'message' => $config->is_active ? 'Algorithm activated' : 'Algorithm deactivated',
        ]);
    }

    /**
     * Update algorithm configuration
     */
    public function updateConfig(Request $request, string $algorithmName)
    {
        $config = AlgorithmConfig::where('algorithm_name', $algorithmName)->firstOrFail();

        $validated = $request->validate([
            'parameters' => 'required|array',
        ]);

        $config->update(['parameters' => $validated['parameters']]);

        return response()->json([
            'success' => true,
            'message' => 'Configuration updated successfully',
        ]);
    }

    /**
     * Run specific algorithm manually
     */
    public function runAlgorithm(Request $request, string $algorithmName)
    {
        try {
            $result = match($algorithmName) {
                'auto_assignment' => (new AutoAssignmentService())->run(),
                'priority_calculation' => (new PriorityCalculationService())->calculateAll(),
                'load_balancing' => (new LoadBalancingService())->analyze(),
                default => throw new \Exception('Unknown algorithm'),
            };

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => 'Algorithm executed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get metrics data for charts (AJAX)
     */
    public function getMetrics(Request $request, string $algorithmName)
    {
        $hours = $request->input('hours', 24);

        $metrics = AlgorithmMetric::forAlgorithm($algorithmName)
            ->recent($hours)
            ->orderBy('recorded_at')
            ->get()
            ->groupBy('metric_name');

        $chartData = [];
        foreach ($metrics as $metricName => $data) {
            $chartData[$metricName] = [
                'labels' => $data->pluck('recorded_at')->map(fn($date) => $date->format('H:i'))->toArray(),
                'data' => $data->pluck('value')->toArray(),
            ];
        }

        return response()->json($chartData);
    }

    /**
     * Get recent logs (AJAX)
     */
    public function getLogs(Request $request)
    {
        $algorithmName = $request->input('algorithm');
        $limit = $request->input('limit', 50);

        $query = AlgorithmLog::with(['workOrder', 'user'])->latest();

        if ($algorithmName) {
            $query->where('algorithm_name', $algorithmName);
        }

        $logs = $query->take($limit)->get();

        return response()->json($logs);
    }

    /**
     * Calculate overall system health
     */
    protected function calculateOverallHealth($algorithms, $loadAnalysis): string
    {
        // Check if any algorithm has error status
        $hasErrors = $algorithms->contains('status', 'error');
        if ($hasErrors) {
            return 'critical';
        }

        // Check load balancing health
        if ($loadAnalysis['health_status'] === 'critical') {
            return 'critical';
        } elseif ($loadAnalysis['health_status'] === 'warning') {
            return 'warning';
        }

        // Check if critical algorithms are active
        $criticalAlgos = ['auto_assignment', 'priority_calculation'];
        $activeCount = $algorithms->whereIn('algorithm_name', $criticalAlgos)
            ->where('is_active', true)
            ->count();

        if ($activeCount === 0) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Calculate automation rate
     */
    protected function calculateAutomationRate(): float
    {
        // Get total automated actions in last 24 hours
        $automatedActions = AlgorithmLog::where('created_at', '>=', now()->subHours(24))
            ->where('result', 'success')
            ->count();

        // Get total manual actions (assuming WorkOrderLog tracks manual changes)
        $totalActions = \App\Models\WorkOrderLog::where('created_at', '>=', now()->subHours(24))
            ->count();

        if ($totalActions === 0) {
            return 0;
        }

        return ($automatedActions / $totalActions) * 100;
    }
}
