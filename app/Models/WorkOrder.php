<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'spk_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'shoe_brand',
        'shoe_type',
        'shoe_color',
        'shoe_size',
        'category',
        'status',
        'current_location',
        'notes',
        'entry_date',
        'estimation_date',
        'finished_date',
        'taken_date',
        'priority',
        'created_by',
        // New Assignment Columns
        'pic_sortir_sol_id',
        'pic_sortir_upper_id',
        'technician_production_id',
        'qc_jahit_technician_id',
        'qc_cleanup_technician_id',
        'qc_final_pic_id',
        // Preparation Tracking
        'prep_washing_started_at', 'prep_washing_completed_at', 'prep_washing_by',
        'prep_sol_started_at', 'prep_sol_completed_at', 'prep_sol_by',
        'prep_upper_started_at', 'prep_upper_completed_at', 'prep_upper_by',
        // Production Tracking
        'prod_sol_started_at', 'prod_sol_completed_at', 'prod_sol_by',
        'prod_upper_started_at', 'prod_upper_completed_at', 'prod_upper_by',
        'prod_cleaning_started_at', 'prod_cleaning_completed_at', 'prod_cleaning_by',
        // QC Tracking
        'qc_jahit_started_at', 'qc_jahit_completed_at', 'qc_jahit_by',
        'qc_cleanup_started_at', 'qc_cleanup_completed_at', 'qc_cleanup_by',
        'qc_final_started_at', 'qc_final_completed_at', 'qc_final_by',
        'is_revising',
        // Reception
        'accessories_data',
        'reception_qc_passed',
        'reception_rejection_reason',
        // Finance columns
        'transaction_type',
        'source_jasa',
        'total_service_price',
        'cost_oto',
        'cost_add_service',
        'shipping_cost',
        'shipping_type',
        'shipping_zone',
        'payment_status_detail',
        'payment_due_date',
        'final_status',
        'finance_entry_at',
        'finance_exit_at',
        'pic_finance_id',
        'previous_status', // Track status before FollowUp
        // Warehouse Reception
        'accessories_tali',
        'accessories_insole',
        'accessories_box',
        'accessories_other',
        'warehouse_qc_status',
        'warehouse_qc_notes',
        'warehouse_qc_by',
        'warehouse_qc_by',
        'warehouse_qc_at',
        'technician_notes', // Technical Instructions from Assessment
        // Storage Tracking
        'storage_rack_code',
        'stored_at',
        'retrieved_at',
        'unique_code',
        'reminder_count',
        'last_reminder_at',
        'donated_at',
    ];

    protected $casts = [
        'status' => \App\Enums\WorkOrderStatus::class, // Enum Casting
        'entry_date' => 'datetime',
        'estimation_date' => 'datetime',
        'finished_date' => 'datetime',
        'taken_date' => 'datetime',
        'payment_due_date' => 'datetime', 
        'last_reminder_at' => 'datetime',
        'donated_at' => 'datetime',
        // Preparation
        'prep_washing_started_at' => 'datetime', 'prep_washing_completed_at' => 'datetime',
        'prep_sol_started_at' => 'datetime', 'prep_sol_completed_at' => 'datetime',
        'prep_upper_started_at' => 'datetime', 'prep_upper_completed_at' => 'datetime',
        // Production
        'prod_sol_started_at' => 'datetime', 'prod_sol_completed_at' => 'datetime',
        'prod_upper_started_at' => 'datetime', 'prod_upper_completed_at' => 'datetime',
        'prod_cleaning_started_at' => 'datetime', 'prod_cleaning_completed_at' => 'datetime',
        // QC
        'qc_jahit_started_at' => 'datetime', 'qc_jahit_completed_at' => 'datetime',
        'qc_cleanup_started_at' => 'datetime', 'qc_cleanup_completed_at' => 'datetime',
        'qc_final_started_at' => 'datetime', 'qc_final_completed_at' => 'datetime',
        'accessories_data' => 'array',
        'reception_qc_passed' => 'boolean',
        'warehouse_qc_at' => 'datetime',
        'finance_entry_at' => 'datetime',
        'finance_exit_at' => 'datetime',
        'phone_normalized' => 'boolean', // Flag for normalization if needed
    ];

    /**
     * Normalize customer phone number before saving
     */
    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['customer_phone'] = \App\Helpers\PhoneHelper::normalize($value);
    }


    public function prodSolBy() { return $this->belongsTo(User::class, 'prod_sol_by'); }
    public function prodUpperBy() { return $this->belongsTo(User::class, 'prod_upper_by'); }
    public function prodCleaningBy() { return $this->belongsTo(User::class, 'prod_cleaning_by'); }

    public function qcJahitBy() { return $this->belongsTo(User::class, 'qc_jahit_by'); }
    public function qcCleanupBy() { return $this->belongsTo(User::class, 'qc_cleanup_by'); }
    public function qcFinalBy() { return $this->belongsTo(User::class, 'qc_final_by'); }




    public function logs()
    {
        return $this->hasMany(WorkOrderLog::class);
    }

    public function photos()
    {
        return $this->hasMany(WorkOrderPhoto::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\OrderPayment::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_phone', 'phone');
    }

    public function picFinance()
    {
        return $this->belongsTo(User::class, 'pic_finance_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function cxIssues()
    {
        return $this->hasMany(CxIssue::class);
    }

    // Preparation Accessors
    public function getNeedsSolAttribute(): bool
    {
        return $this->services->contains(function($s) {
            return stripos($s->category, 'Sol') !== false || stripos($s->name, 'Sol') !== false;
        });
    }

    public function getNeedsUpperAttribute(): bool
    {
        return $this->services->contains(function($s) {
            return stripos($s->category, 'Upper') !== false || 
                   stripos($s->name, 'Upper') !== false ||
                   stripos($s->category, 'Repaint') !== false ||
                   stripos($s->name, 'Repaint') !== false ||
                   stripos($s->category, 'Jahit') !== false ||
                   stripos($s->name, 'Jahit') !== false;
        });
    }

    public function getIsReadyAttribute(): bool
    {
        $doneWashing = !is_null($this->prep_washing_completed_at);
        $doneSol = !$this->needs_sol || !is_null($this->prep_sol_completed_at);
        $doneUpper = !$this->needs_upper || !is_null($this->prep_upper_completed_at);

        return $doneWashing && $doneSol && $doneUpper;
    }

    public function getIsProductionFinishedAttribute(): bool
    {
        // 1. Sol
        $needsSol = $this->services->contains(fn($s) => stripos($s->category, 'sol') !== false || stripos($s->name, 'Sol') !== false);
        $doneSol = !$needsSol || !is_null($this->prod_sol_completed_at);

        // 2. Upper
        $needsUpper = $this->services->contains(fn($s) => stripos($s->category, 'upper') !== false);
        $doneUpper = !$needsUpper || !is_null($this->prod_upper_completed_at);

        // 3. Treatment / Cleaning / Repaint
        $needsTreatment = $this->services->contains(fn($s) => 
            stripos($s->category, 'cleaning') !== false || 
            stripos($s->category, 'whitening') !== false || 
            stripos($s->category, 'repaint') !== false ||
            stripos($s->category, 'treatment') !== false
        );
        $doneTreatment = !$needsTreatment || !is_null($this->prod_cleaning_completed_at);

        return $doneSol && $doneUpper && $doneTreatment;
    }

    public function getIsQcFinishedAttribute(): bool
    {
        // 1. QC Jahit (Only if needed)
        $needsJahit = $this->services->contains(fn($s) => 
            stripos($s->category, 'sol') !== false || 
            stripos($s->category, 'upper') !== false || 
            stripos($s->category, 'repaint') !== false
        );
        $doneJahit = !$needsJahit || !is_null($this->qc_jahit_completed_at);

        // 2. QC Cleanup (Mandatory)
        $doneCleanup = !is_null($this->qc_cleanup_completed_at);

        // 3. QC Final (Mandatory)
        $doneFinal = !is_null($this->qc_final_completed_at);

        return $doneJahit && $doneCleanup && $doneFinal;
    }

    public function getIsSortirFinishedAttribute(): bool
    {
        // Check if any material is still REQUESTED (not READY/ALLOCATED)
        // usage existing relation if loaded, otherwise query
        if ($this->relationLoaded('materials')) {
            return !$this->materials->contains(function($m) {
                return $m->pivot->status === 'REQUESTED';
            });
        }
        
        return $this->materials()->wherePivot('status', 'REQUESTED')->count() === 0;
    }

    public function getTotalPriceAttribute()
    {
        return $this->services()->sum('work_order_services.cost');
    }

    // Relationships for Technicians/PICs
    public function picSortirSol() { return $this->belongsTo(User::class, 'pic_sortir_sol_id'); }
    public function picSortirUpper() { return $this->belongsTo(User::class, 'pic_sortir_upper_id'); }
    public function technicianProduction() { return $this->belongsTo(User::class, 'technician_production_id'); }
    public function qcJahitTechnician() { return $this->belongsTo(User::class, 'qc_jahit_technician_id'); }
    public function qcCleanupTechnician() { return $this->belongsTo(User::class, 'qc_cleanup_technician_id'); }
    public function qcFinalPic() { return $this->belongsTo(User::class, 'qc_final_pic_id'); }
    
    // Preparation Technicians
    public function prepWashingBy() { return $this->belongsTo(User::class, 'prep_washing_by'); }
    public function prepSolBy() { return $this->belongsTo(User::class, 'prep_sol_by'); }
    public function prepUpperBy() { return $this->belongsTo(User::class, 'prep_upper_by'); }

    // Enhanced Service Relationship
    // 1. Pivot Relation (Standard)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'work_order_services')
            ->using(WorkOrderService::class)
            ->withPivot(['id', 'cost', 'status', 'technician_id', 'custom_service_name', 'category_name', 'service_details'])
            ->withTimestamps();
    }
    
    // 2. Direct Relation (For Custom Services support which have null service_id)
    public function workOrderServices()
    {
        return $this->hasMany(WorkOrderService::class);
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'work_order_materials')
            ->withPivot(['id', 'quantity', 'status'])
            ->withTimestamps();
    }

    public function storageAssignments()
    {
        return $this->hasMany(\App\Models\StorageAssignment::class);
    }

    // ========================================
    // Countdown System Accessors
    // ========================================

    /**
     * Get days remaining until estimation date
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->estimation_date) {
            return null;
        }
        return \App\Helpers\DateHelper::calculateDaysRemaining($this->estimation_date);
    }

    /**
     * Get urgency level based on days remaining
     */
    public function getUrgencyLevelAttribute()
    {
        $days = $this->days_remaining;
        if ($days === null) {
            return 'unknown';
        }
        return \App\Helpers\DateHelper::getUrgencyLevel($days);
    }

    /**
     * Check if order is overdue
     */
    public function getIsOverdueAttribute()
    {
        if (!$this->estimation_date) {
            return false;
        }
        return $this->days_remaining === 0 && 
               \Carbon\Carbon::parse($this->estimation_date)->isPast();
    }
    /**
     * Get the photo path to be used as SPK Cover
     */
    public function getSpkCoverPhotoAttribute()
    {
        // 1. Manually selected cover
        $cover = $this->photos()->where('is_spk_cover', true)->first();
        if ($cover) {
            return $cover->file_path;
        }

        // 2. Fallback: Reception Photo (WAREHOUSE_BEFORE)
        $reception = $this->photos()->where('step', 'WAREHOUSE_BEFORE')->first();
        if ($reception) {
            return $reception->file_path;
        }

        // 3. Last Fallback: First available photo
        $first = $this->photos()->first();
        return $first ? $first->file_path : null;
    }

    /**
     * Generate unique code between 1-999 if not exists
     */
    /**
     * Generate unique code between 100-999 if not exists
     * Ensures uniqueness among ACTIVE (Unpaid/Partial) transactions only.
     */
    public function ensureUniqueCode()
    {
        if ($this->unique_code) {
            return $this->unique_code;
        }

        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            $code = rand(100, 999);
            $attempt++;
            
            // Check collision only against UNPAID transactions
            // Exclude self and cancelled/completed-paid orders
            $exists = static::where('unique_code', $code)
                ->where('id', '!=', $this->id)
                ->where(function($q) {
                    $q->whereNull('status_pembayaran')
                      ->orWhere('status_pembayaran', '!=', 'L'); // Hanya yang belum lunas
                })
                ->exists();

            if (!$exists) {
                $this->unique_code = $code;
                $this->save();
                return $code;
            }

        } while ($attempt < $maxAttempts);

        // Fallback if very busy: Allow > 1000 or duplicate (rare case)
        $this->unique_code = rand(1000, 9999); // Expand range
        $this->save();
        
        return $this->unique_code;
    }
}

