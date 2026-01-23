<?php

namespace App\Services\AlgorithmManagement;

use App\Models\WorkOrder;
use App\Models\User;
use App\Models\AlgorithmConfig;
use App\Models\AlgorithmLog;
use App\Models\AlgorithmMetric;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;

class AutoAssignmentService
{
    protected AlgorithmConfig $config;
    protected string $algorithmName = 'auto_assignment';

    public function __construct()
    {
        $this->config = AlgorithmConfig::firstOrCreate(
            ['algorithm_name' => $this->algorithmName],
            [
                'description' => 'Automatically assign orders to technicians based on workload, skill, and performance',
                'is_active' => false, // Start disabled for safety
                'parameters' => [
                    'max_concurrent_orders' => 5,
                    'skill_matching_weight' => 60,
                    'performance_weight' => 30,
                    'workload_weight' => 10,
                    'auto_assign_production' => true,
                    'auto_assign_qc' => false,
                ]
            ]
        );
    }

    /**
     * Run auto-assignment for eligible orders
     */
    public function run(): array
    {
        if (!$this->config->is_active) {
            return ['status' => 'skipped', 'reason' => 'Algorithm is disabled'];
        }

        $this->config->markAsRunning();
        $startTime = microtime(true);
        $results = ['assigned' => 0, 'skipped' => 0, 'failed' => 0];

        try {
            // Get unassigned production orders
            if ($this->config->getParameter('auto_assign_production')) {
                $productionOrders = WorkOrder::where('status', WorkOrderStatus::PRODUCTION)
                    ->whereNull('technician_production_id')
                    ->get();

                foreach ($productionOrders as $order) {
                    $result = $this->assignToProduction($order);
                    $results[$result]++;
                }
            }

            // Get unassigned QC orders
            if ($this->config->getParameter('auto_assign_qc')) {
                $qcOrders = WorkOrder::where('status', WorkOrderStatus::QC)
                    ->whereNull('qc_final_pic_id')
                    ->get();

                foreach ($qcOrders as $order) {
                    $result = $this->assignToQC($order);
                    $results[$result]++;
                }
            }

            $this->config->markAsIdle();
            $this->recordMetrics($results);

        } catch (\Exception $e) {
            $this->config->markAsError($e->getMessage());
            $results['error'] = $e->getMessage();
        }

        $executionTime = (microtime(true) - $startTime) * 1000;
        $this->recordExecutionTime($executionTime);

        return $results;
    }

    /**
     * Assign order to production technician
     */
    protected function assignToProduction(WorkOrder $order): string
    {
        $startTime = microtime(true);

        try {
            $technician = $this->findBestTechnician($order, 'production');

            if (!$technician) {
                $this->logAction($order, null, 'assign_production', 'failed', 'No suitable technician found');
                return 'skipped';
            }

            $order->update(['technician_production_id' => $technician->id]);

            $executionTime = (microtime(true) - $startTime) * 1000;
            $this->logAction($order, $technician, 'assign_production', 'success', null, $executionTime);

            return 'assigned';

        } catch (\Exception $e) {
            $this->logAction($order, null, 'assign_production', 'failed', $e->getMessage());
            return 'failed';
        }
    }

    /**
     * Assign order to QC technician
     */
    protected function assignToQC(WorkOrder $order): string
    {
        $startTime = microtime(true);

        try {
            $technician = $this->findBestTechnician($order, 'qc');

            if (!$technician) {
                $this->logAction($order, null, 'assign_qc', 'failed', 'No suitable technician found');
                return 'skipped';
            }

            $order->update(['qc_final_pic_id' => $technician->id]);

            $executionTime = (microtime(true) - $startTime) * 1000;
            $this->logAction($order, $technician, 'assign_qc', 'success', null, $executionTime);

            return 'assigned';

        } catch (\Exception $e) {
            $this->logAction($order, null, 'assign_qc', 'failed', $e->getMessage());
            return 'failed';
        }
    }

    /**
     * Find best technician using weighted scoring
     */
    protected function findBestTechnician(WorkOrder $order, string $type): ?User
    {
        $maxConcurrent = $this->config->getParameter('max_concurrent_orders', 5);

        // Get eligible technicians (not customers, not overloaded)
        $technicians = User::where('role', '!=', 'customer')
            ->withCount([
                'jobsProduction as active_production_count' => function ($q) {
                    $q->where('status', WorkOrderStatus::PRODUCTION);
                },
                'jobsQcFinal as active_qc_count' => function ($q) {
                    $q->where('status', WorkOrderStatus::QC);
                }
            ])
            ->get()
            ->filter(function ($tech) use ($maxConcurrent) {
                $totalActive = $tech->active_production_count + $tech->active_qc_count;
                return $totalActive < $maxConcurrent;
            });

        if ($technicians->isEmpty()) {
            return null;
        }

        // Calculate scores for each technician
        $scored = $technicians->map(function ($tech) use ($order, $type) {
            $score = 0;

            // Workload score (lower is better)
            $workloadScore = $this->calculateWorkloadScore($tech);
            $score += $workloadScore * ($this->config->getParameter('workload_weight', 10) / 100);

            // Skill matching score
            $skillScore = $this->calculateSkillScore($tech, $order);
            $score += $skillScore * ($this->config->getParameter('skill_matching_weight', 60) / 100);

            // Performance score
            $performanceScore = $this->calculatePerformanceScore($tech);
            $score += $performanceScore * ($this->config->getParameter('performance_weight', 30) / 100);

            $tech->assignment_score = $score;
            return $tech;
        });

        // Return technician with highest score
        return $scored->sortByDesc('assignment_score')->first();
    }

    /**
     * Calculate workload score (0-100, higher = less loaded)
     */
    protected function calculateWorkloadScore(User $technician): float
    {
        $maxConcurrent = $this->config->getParameter('max_concurrent_orders', 5);
        $currentLoad = $technician->active_production_count + $technician->active_qc_count;

        // Invert: less load = higher score
        return (($maxConcurrent - $currentLoad) / $maxConcurrent) * 100;
    }

    /**
     * Calculate skill matching score (0-100)
     * TODO: Implement skill-based matching when skill data is available
     */
    protected function calculateSkillScore(User $technician, WorkOrder $order): float
    {
        // Placeholder: return neutral score
        // In future, match technician skills with order service categories
        return 50;
    }

    /**
     * Calculate performance score (0-100)
     */
    protected function calculatePerformanceScore(User $technician): float
    {
        // Get completion rate from last 30 days
        $completed = WorkOrder::where('qc_final_pic_id', $technician->id)
            ->where('status', WorkOrderStatus::SELESAI)
            ->where('qc_final_completed_at', '>=', now()->subDays(30))
            ->count();

        $total = WorkOrder::where('qc_final_pic_id', $technician->id)
            ->where('qc_final_completed_at', '>=', now()->subDays(30))
            ->count();

        if ($total === 0) {
            return 50; // Neutral score for new technicians
        }

        return ($completed / $total) * 100;
    }

    /**
     * Log assignment action
     */
    protected function logAction(
        WorkOrder $order,
        ?User $technician,
        string $actionType,
        string $result,
        ?string $errorMessage = null,
        ?float $executionTime = null
    ): void {
        AlgorithmLog::create([
            'algorithm_name' => $this->algorithmName,
            'action_type' => $actionType,
            'work_order_id' => $order->id,
            'user_id' => $technician?->id,
            'metadata' => [
                'spk_number' => $order->spk_number,
                'technician_name' => $technician?->name,
                'assignment_score' => $technician?->assignment_score ?? null,
            ],
            'result' => $result,
            'error_message' => $errorMessage,
            'execution_time_ms' => $executionTime,
        ]);
    }

    /**
     * Record performance metrics
     */
    protected function recordMetrics(array $results): void
    {
        $total = $results['assigned'] + $results['skipped'] + $results['failed'];
        $successRate = $total > 0 ? ($results['assigned'] / $total) * 100 : 0;

        AlgorithmMetric::create([
            'algorithm_name' => $this->algorithmName,
            'metric_name' => 'assignment_success_rate',
            'value' => $successRate,
            'unit' => '%',
            'recorded_at' => now(),
            'metadata' => $results,
        ]);

        AlgorithmMetric::create([
            'algorithm_name' => $this->algorithmName,
            'metric_name' => 'assignments_count',
            'value' => $results['assigned'],
            'unit' => 'count',
            'recorded_at' => now(),
        ]);
    }

    /**
     * Record execution time metric
     */
    protected function recordExecutionTime(float $executionTime): void
    {
        AlgorithmMetric::create([
            'algorithm_name' => $this->algorithmName,
            'metric_name' => 'execution_time',
            'value' => $executionTime,
            'unit' => 'ms',
            'recorded_at' => now(),
        ]);
    }
}
