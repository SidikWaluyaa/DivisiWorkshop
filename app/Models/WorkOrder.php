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
        'is_revising'
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'estimation_date' => 'datetime',
        'finished_date' => 'datetime',
        'taken_date' => 'datetime',
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
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'work_order_services')
                    ->withPivot('cost', 'status', 'technician_id')
                    ->withTimestamps();
    }



    public function prodSolBy() { return $this->belongsTo(User::class, 'prod_sol_by'); }
    public function prodUpperBy() { return $this->belongsTo(User::class, 'prod_upper_by'); }
    public function prodCleaningBy() { return $this->belongsTo(User::class, 'prod_cleaning_by'); }

    public function qcJahitBy() { return $this->belongsTo(User::class, 'qc_jahit_by'); }
    public function qcCleanupBy() { return $this->belongsTo(User::class, 'qc_cleanup_by'); }
    public function qcFinalBy() { return $this->belongsTo(User::class, 'qc_final_by'); }


    public function materials()
    {
        return $this->belongsToMany(Material::class, 'work_order_materials')
                    ->withPivot('quantity', 'status')
                    ->withTimestamps();
    }

    public function logs()
    {
        return $this->hasMany(WorkOrderLog::class);
    }

    public function photos()
    {
        return $this->hasMany(WorkOrderPhoto::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
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
            return stripos($s->category, 'Upper') !== false;
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
        return $this->services->sum('pivot.cost');
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
}
