<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlgorithmConfig extends Model
{
    protected $fillable = [
        'algorithm_name',
        'is_active',
        'parameters',
        'description',
        'last_run_at',
        'status',
        'last_error',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get parameter value by key
     */
    public function getParameter(string $key, $default = null)
    {
        return data_get($this->parameters, $key, $default);
    }

    /**
     * Set parameter value
     */
    public function setParameter(string $key, $value): void
    {
        $params = $this->parameters ?? [];
        data_set($params, $key, $value);
        $this->parameters = $params;
        $this->save();
    }

    /**
     * Get logs for this algorithm
     */
    public function logs()
    {
        return $this->hasMany(AlgorithmLog::class, 'algorithm_name', 'algorithm_name');
    }

    /**
     * Get metrics for this algorithm
     */
    public function metrics()
    {
        return $this->hasMany(AlgorithmMetric::class, 'algorithm_name', 'algorithm_name');
    }

    /**
     * Check if algorithm is currently running
     */
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Mark as running
     */
    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'last_run_at' => now(),
        ]);
    }

    /**
     * Mark as idle
     */
    public function markAsIdle(): void
    {
        $this->update(['status' => 'idle']);
    }

    /**
     * Mark as error
     */
    public function markAsError(string $error): void
    {
        $this->update([
            'status' => 'error',
            'last_error' => $error,
        ]);
    }
}
