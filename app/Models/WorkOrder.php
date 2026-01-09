<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = [
        'spk_number',
        'customer_name',
        'customer_phone',
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
        'qc_final_pic_id'
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'estimation_date' => 'datetime',
        'finished_date' => 'datetime',
        'taken_date' => 'datetime',
        // 'status' => \App\Enums\WorkOrderStatus::class, // Commented out until Enum is fully linked/proven to not cause issues if string mismatches, but recommended.
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'work_order_services')
                    ->withPivot('cost', 'status', 'technician_id')
                    ->withTimestamps();
    }

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

    // Relationships for Technicians/PICs
    public function picSortirSol() { return $this->belongsTo(User::class, 'pic_sortir_sol_id'); }
    public function picSortirUpper() { return $this->belongsTo(User::class, 'pic_sortir_upper_id'); }
    public function technicianProduction() { return $this->belongsTo(User::class, 'technician_production_id'); }
    public function qcJahitTechnician() { return $this->belongsTo(User::class, 'qc_jahit_technician_id'); }
    public function qcCleanupTechnician() { return $this->belongsTo(User::class, 'qc_cleanup_technician_id'); }
    public function qcFinalPic() { return $this->belongsTo(User::class, 'qc_final_pic_id'); }
}
