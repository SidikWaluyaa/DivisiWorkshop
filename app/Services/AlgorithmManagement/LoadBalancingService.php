<?php

namespace App\Services\AlgorithmManagement;

use App\Models\WorkOrder;
use App\Models\AlgorithmConfig;
use App\Models\AlgorithmMetric;
use App\Enums\WorkOrderStatus;

class LoadBalancingService
{
    protected AlgorithmConfig $config;
    protected string $algorithmName = 'load_balancing';

    public function __construct()
    {
        $this->config = AlgorithmConfig::firstOrCreate(
            ['algorithm_name' => $this->algorithmName],
            [
                'description' => 'Monitor and balance workload across workshop stations',
                'is_active' => true,
                'parameters' => [
                    'bottleneck_threshold' => 15,
                    'warning_threshold' => 10,
                    'enable_alerts' => true,
                    'enable_recommendations' => true,
                ]
            ]
        );
    }

    /**
     * Analyze current load distribution
     */
    public function analyze(): array
    {
        $workload = $this->getWorkloadByStation();
        $bottlenecks = $this->detectBottlenecks($workload);
        $recommendations = $this->generateRecommendations($workload, $bottlenecks);

        // Record metrics
        $this->recordMetrics($workload);

        return [
            'workload' => $workload,
            'bottlenecks' => $bottlenecks,
            'recommendations' => $recommendations,
            'health_status' => $this->calculateHealthStatus($workload),
        ];
    }

    /**
     * Get current workload by station
     */
    protected function getWorkloadByStation(): array
    {
        return [
            'assessment' => WorkOrder::where('status', WorkOrderStatus::ASSESSMENT)->count(),
            'preparation' => WorkOrder::where('status', WorkOrderStatus::PREPARATION)->count(),
            'sortir' => WorkOrder::where('status', WorkOrderStatus::SORTIR)->count(),
            'production' => WorkOrder::where('status', WorkOrderStatus::PRODUCTION)->count(),
            'qc' => WorkOrder::where('status', WorkOrderStatus::QC)->count(),
        ];
    }

    /**
     * Detect bottlenecks
     */
    protected function detectBottlenecks(array $workload): array
    {
        $threshold = $this->config->getParameter('bottleneck_threshold', 15);
        $warningThreshold = $this->config->getParameter('warning_threshold', 10);

        $bottlenecks = [];

        foreach ($workload as $station => $count) {
            if ($count >= $threshold) {
                $bottlenecks[] = [
                    'station' => $station,
                    'count' => $count,
                    'severity' => 'critical',
                    'message' => "Station {$station} has {$count} orders (threshold: {$threshold})",
                ];
            } elseif ($count >= $warningThreshold) {
                $bottlenecks[] = [
                    'station' => $station,
                    'count' => $count,
                    'severity' => 'warning',
                    'message' => "Station {$station} approaching capacity with {$count} orders",
                ];
            }
        }

        return $bottlenecks;
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations(array $workload, array $bottlenecks): array
    {
        if (!$this->config->getParameter('enable_recommendations', true)) {
            return [];
        }

        $recommendations = [];

        foreach ($bottlenecks as $bottleneck) {
            $station = $bottleneck['station'];

            switch ($station) {
                case 'preparation':
                    $recommendations[] = [
                        'station' => $station,
                        'action' => 'Add more preparation staff or extend working hours',
                        'priority' => $bottleneck['severity'] === 'critical' ? 'high' : 'medium',
                    ];
                    break;

                case 'production':
                    $recommendations[] = [
                        'station' => $station,
                        'action' => 'Consider outsourcing or hiring temporary technicians',
                        'priority' => $bottleneck['severity'] === 'critical' ? 'high' : 'medium',
                    ];
                    break;

                case 'qc':
                    $recommendations[] = [
                        'station' => $station,
                        'action' => 'Expedite QC process or add QC personnel',
                        'priority' => $bottleneck['severity'] === 'critical' ? 'high' : 'medium',
                    ];
                    break;

                case 'sortir':
                    $recommendations[] = [
                        'station' => $station,
                        'action' => 'Check material availability and procurement speed',
                        'priority' => $bottleneck['severity'] === 'critical' ? 'high' : 'medium',
                    ];
                    break;
            }
        }

        return $recommendations;
    }

    /**
     * Calculate overall health status
     */
    protected function calculateHealthStatus(array $workload): string
    {
        $threshold = $this->config->getParameter('bottleneck_threshold', 15);
        $warningThreshold = $this->config->getParameter('warning_threshold', 10);

        $criticalCount = count(array_filter($workload, fn($count) => $count >= $threshold));
        $warningCount = count(array_filter($workload, fn($count) => $count >= $warningThreshold && $count < $threshold));

        if ($criticalCount > 0) {
            return 'critical';
        } elseif ($warningCount > 0) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }

    /**
     * Record metrics
     */
    protected function recordMetrics(array $workload): void
    {
        foreach ($workload as $station => $count) {
            AlgorithmMetric::create([
                'algorithm_name' => $this->algorithmName,
                'metric_name' => "queue_length_{$station}",
                'value' => $count,
                'unit' => 'count',
                'recorded_at' => now(),
            ]);
        }

        // Record average queue length
        $avgQueueLength = array_sum($workload) / count($workload);
        AlgorithmMetric::create([
            'algorithm_name' => $this->algorithmName,
            'metric_name' => 'avg_queue_length',
            'value' => $avgQueueLength,
            'unit' => 'count',
            'recorded_at' => now(),
        ]);

        // Record max queue length
        $maxQueueLength = max($workload);
        AlgorithmMetric::create([
            'algorithm_name' => $this->algorithmName,
            'metric_name' => 'max_queue_length',
            'value' => $maxQueueLength,
            'unit' => 'count',
            'recorded_at' => now(),
        ]);
    }

    /**
     * Get historical trend
     */
    public function getTrend(int $hours = 24): array
    {
        $metrics = AlgorithmMetric::forAlgorithm($this->algorithmName)
            ->where('metric_name', 'avg_queue_length')
            ->recent($hours)
            ->orderBy('recorded_at')
            ->get();

        return [
            'labels' => $metrics->pluck('recorded_at')->map(fn($date) => $date->format('H:i'))->toArray(),
            'data' => $metrics->pluck('value')->toArray(),
        ];
    }
}
