<?php

namespace App\Services\AlgorithmManagement;

use App\Models\WorkOrder;
use App\Models\AlgorithmConfig;
use App\Models\AlgorithmLog;
use App\Models\AlgorithmMetric;
use App\Enums\WorkOrderStatus;

class PriorityCalculationService
{
    protected AlgorithmConfig $config;
    protected string $algorithmName = 'priority_calculation';

    public function __construct()
    {
        $this->config = AlgorithmConfig::firstOrCreate(
            ['algorithm_name' => $this->algorithmName],
            [
                'description' => 'Calculate dynamic priority scores for orders based on multiple factors',
                'is_active' => true,
                'parameters' => [
                    'deadline_weight' => 50,
                    'value_weight' => 20,
                    'customer_tier_weight' => 20,
                    'revision_bonus' => 10,
                    'vip_multiplier' => 1.5,
                ]
            ]
        );
    }

    /**
     * Calculate priority for all active orders
     */
    public function calculateAll(): array
    {
        $startTime = microtime(true);
        $updated = 0;

        $activeOrders = WorkOrder::whereIn('status', [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ])->get();

        foreach ($activeOrders as $order) {
            $priority = $this->calculatePriority($order);
            
            // Store in metadata or add priority_score column
            $order->update(['priority_score' => $priority]);
            $updated++;

            $this->logCalculation($order, $priority);
        }

        $executionTime = (microtime(true) - $startTime) * 1000;
        $this->recordMetrics($activeOrders, $executionTime);

        return [
            'updated' => $updated,
            'execution_time_ms' => $executionTime,
        ];
    }

    /**
     * Calculate priority score for a single order (0-100)
     */
    public function calculatePriority(WorkOrder $order): float
    {
        $score = 0;

        // 1. Deadline Factor (0-100)
        $deadlineScore = $this->calculateDeadlineScore($order);
        $score += $deadlineScore * ($this->config->getParameter('deadline_weight', 50) / 100);

        // 2. Order Value Factor (0-100)
        $valueScore = $this->calculateValueScore($order);
        $score += $valueScore * ($this->config->getParameter('value_weight', 20) / 100);

        // 3. Customer Tier Factor (0-100)
        $customerScore = $this->calculateCustomerScore($order);
        $score += $customerScore * ($this->config->getParameter('customer_tier_weight', 20) / 100);

        // 4. Revision Bonus
        if ($order->is_revising) {
            $score += $this->config->getParameter('revision_bonus', 10);
        }

        // 5. VIP Multiplier
        if ($this->isVIPCustomer($order)) {
            $score *= $this->config->getParameter('vip_multiplier', 1.5);
        }

        // Cap at 100
        return min($score, 100);
    }

    /**
     * Calculate deadline-based score (0-100, higher = more urgent)
     */
    protected function calculateDeadlineScore(WorkOrder $order): float
    {
        if (!$order->estimation_date) {
            return 50; // Neutral score if no deadline
        }

        $daysRemaining = now()->diffInDays($order->estimation_date, false);

        // Scoring logic:
        // Overdue (< 0 days) = 100
        // 0-1 days = 90-100
        // 1-3 days = 70-90
        // 3-7 days = 40-70
        // 7+ days = 0-40

        if ($daysRemaining < 0) {
            return 100; // Overdue
        } elseif ($daysRemaining <= 1) {
            return 90 + (1 - $daysRemaining) * 10;
        } elseif ($daysRemaining <= 3) {
            return 70 + ((3 - $daysRemaining) / 2) * 20;
        } elseif ($daysRemaining <= 7) {
            return 40 + ((7 - $daysRemaining) / 4) * 30;
        } else {
            return max(0, 40 - ($daysRemaining - 7) * 2);
        }
    }

    /**
     * Calculate value-based score (0-100)
     */
    protected function calculateValueScore(WorkOrder $order): float
    {
        $value = $order->total_service_price ?? 0;

        // Normalize based on typical order values
        // Assuming typical range: 50k - 500k
        $minValue = 50000;
        $maxValue = 500000;

        if ($value <= $minValue) {
            return 0;
        } elseif ($value >= $maxValue) {
            return 100;
        } else {
            return (($value - $minValue) / ($maxValue - $minValue)) * 100;
        }
    }

    /**
     * Calculate customer tier score (0-100)
     * TODO: Implement customer tier system
     */
    protected function calculateCustomerScore(WorkOrder $order): float
    {
        // Placeholder: return neutral score
        // In future, check customer tier (VIP, Regular, New)
        return 50;
    }

    /**
     * Check if customer is VIP
     * TODO: Implement VIP customer detection
     */
    protected function isVIPCustomer(WorkOrder $order): bool
    {
        // Placeholder: check if customer has > 10 completed orders
        $completedOrders = WorkOrder::where('customer_phone', $order->customer_phone)
            ->where('status', WorkOrderStatus::SELESAI)
            ->count();

        return $completedOrders >= 10;
    }

    /**
     * Log priority calculation
     */
    protected function logCalculation(WorkOrder $order, float $priority): void
    {
        AlgorithmLog::create([
            'algorithm_name' => $this->algorithmName,
            'action_type' => 'calculate_priority',
            'work_order_id' => $order->id,
            'metadata' => [
                'spk_number' => $order->spk_number,
                'priority_score' => $priority,
                'days_remaining' => $order->days_remaining,
                'order_value' => $order->total_service_price,
                'is_revising' => $order->is_revising,
            ],
            'result' => 'success',
        ]);
    }

    /**
     * Record metrics
     */
    protected function recordMetrics($orders, float $executionTime): void
    {
        $priorities = $orders->pluck('priority_score')->filter();

        if ($priorities->isNotEmpty()) {
            AlgorithmMetric::create([
                'algorithm_name' => $this->algorithmName,
                'metric_name' => 'avg_priority_score',
                'value' => $priorities->avg(),
                'unit' => 'score',
                'recorded_at' => now(),
            ]);

            AlgorithmMetric::create([
                'algorithm_name' => $this->algorithmName,
                'metric_name' => 'high_priority_count',
                'value' => $priorities->filter(fn($p) => $p >= 80)->count(),
                'unit' => 'count',
                'recorded_at' => now(),
            ]);
        }

        AlgorithmMetric::create([
            'algorithm_name' => $this->algorithmName,
            'metric_name' => 'execution_time',
            'value' => $executionTime,
            'unit' => 'ms',
            'recorded_at' => now(),
        ]);
    }

    /**
     * Get priority distribution
     */
    public function getDistribution(): array
    {
        $orders = WorkOrder::whereIn('status', [
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ])->get();

        return [
            'critical' => $orders->where('priority_score', '>=', 80)->count(),
            'high' => $orders->whereBetween('priority_score', [60, 79])->count(),
            'medium' => $orders->whereBetween('priority_score', [40, 59])->count(),
            'low' => $orders->where('priority_score', '<', 40)->count(),
        ];
    }
}
