<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlgorithmMetric extends Model
{
    protected $fillable = [
        'algorithm_name',
        'metric_name',
        'value',
        'unit',
        'recorded_at',
        'metadata',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'recorded_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the algorithm config
     */
    public function config()
    {
        return $this->belongsTo(AlgorithmConfig::class, 'algorithm_name', 'algorithm_name');
    }

    /**
     * Scope for specific algorithm
     */
    public function scopeForAlgorithm($query, string $algorithmName)
    {
        return $query->where('algorithm_name', $algorithmName);
    }

    /**
     * Scope for specific metric
     */
    public function scopeForMetric($query, string $metricName)
    {
        return $query->where('metric_name', $metricName);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }

    /**
     * Scope for recent metrics
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    /**
     * Get average value for a metric
     */
    public static function getAverage(string $algorithmName, string $metricName, int $hours = 24)
    {
        return static::forAlgorithm($algorithmName)
            ->forMetric($metricName)
            ->recent($hours)
            ->avg('value');
    }

    /**
     * Get latest value for a metric
     */
    public static function getLatest(string $algorithmName, string $metricName)
    {
        return static::forAlgorithm($algorithmName)
            ->forMetric($metricName)
            ->latest('recorded_at')
            ->first()?->value;
    }
}
