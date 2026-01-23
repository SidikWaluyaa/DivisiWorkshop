<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlgorithmLog extends Model
{
    protected $fillable = [
        'algorithm_name',
        'action_type',
        'work_order_id',
        'user_id',
        'metadata',
        'result',
        'error_message',
        'execution_time_ms',
    ];

    protected $casts = [
        'metadata' => 'array',
        'execution_time_ms' => 'decimal:2',
    ];

    /**
     * Get the work order associated with this log
     */
    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the user (technician) associated with this log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the algorithm config
     */
    public function config()
    {
        return $this->belongsTo(AlgorithmConfig::class, 'algorithm_name', 'algorithm_name');
    }

    /**
     * Scope for successful actions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('result', 'success');
    }

    /**
     * Scope for failed actions
     */
    public function scopeFailed($query)
    {
        return $query->where('result', 'failed');
    }

    /**
     * Scope for specific algorithm
     */
    public function scopeForAlgorithm($query, string $algorithmName)
    {
        return $query->where('algorithm_name', $algorithmName);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, int $limit = 50)
    {
        return $query->latest()->limit($limit);
    }
}
