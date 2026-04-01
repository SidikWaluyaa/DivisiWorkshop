<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopManifest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'manifest_number',
        'dispatcher_id',
        'receiver_id',
        'status',
        'notes',
        'dispatched_at',
        'received_at',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // When a manifest is deleted (soft-delete), release all work orders
        static::deleting(function ($manifest) {
            foreach ($manifest->workOrders as $workOrder) {
                $workOrder->update([
                    'workshop_manifest_id' => null,
                    'status' => WorkOrderStatus::READY_TO_DISPATCH
                ]);
                
                $workOrder->logs()->create([
                    'step' => 'LOGISTICS',
                    'action' => 'MANIFEST_CANCELLED',
                    'description' => "Manifest #{$manifest->manifest_number} dihapus. Item dikembalikan ke Pool Gudang.",
                    'user_id' => auth()->id()
                ]);
            }
        });
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
