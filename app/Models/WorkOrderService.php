<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkOrderService extends Pivot
{
    protected $table = 'work_order_services';
    public $incrementing = true;
    
    protected $fillable = [
        'work_order_id',
        'service_id',
        'cost',
        'status',
        'technician_id',
        'custom_service_name',
        'category_name',
        'service_details',
        'started_at',
        'completed_at',
        'actual_duration_minutes',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'service_details' => 'array',
        'cost' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id')->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    /**
     * Resolves the proper service name from custom_service_name or relation
     */
    public function getServiceNameAttribute(): string
    {
        return (string) ($this->custom_service_name ?: ($this->service?->name ?? $this->category_name ?? 'Layanan Workshop'));
    }

    /**
     * Resolves the service category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return (string) ($this->category_name ?: ($this->service?->category?->name ?? 'Layanan'));
    }

    /**
     * Checks if this service was added by CX / OTO
     */
    public function getIsCxAdditionalAttribute(): bool
    {
        return !empty($this->service_details['is_cx_additional']);
    }

    /**
     * Parses service_details JSON into a clean array of strings
     */
    public function getParsedDetailsAttribute(): array
    {
        $details = [];
        if (!empty($this->service_details) && is_array($this->service_details)) {
            if (isset($this->service_details['manual_detail']) && !empty($this->service_details['manual_detail'])) {
                $mDetail = $this->service_details['manual_detail'];
                if (is_array($mDetail)) {
                    foreach ($mDetail as $line) {
                        if (is_string($line) && trim($line) !== '') {
                            $details[] = trim($line);
                        }
                    }
                } elseif (is_string($mDetail) && trim($mDetail) !== '') {
                    $details[] = trim($mDetail);
                }
            } else {
                foreach ($this->service_details as $k => $v) {
                    if (!empty($v) && !in_array($k, ['manual_detail', 'is_cx_additional', 'hk_days'])) {
                        if (is_array($v)) {
                            foreach ($v as $val) {
                                if (is_string($val) && trim($val) !== '') {
                                    $details[] = trim($val);
                                }
                            }
                        } elseif (is_string($v) && trim($v) !== '') {
                            $details[] = is_numeric($k) ? trim($v) : "$k: " . trim($v);
                        }
                    }
                }
            }
        }
        if (!empty($this->notes) && trim($this->notes) !== '') {
            $details[] = trim($this->notes);
        }
        return array_values(array_unique($details));
    }
}
