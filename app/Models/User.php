<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'specialization',
        'access_rights',
        'cs_code',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'access_rights' => 'array',
        ];
    }

    /**
     * Check if user has access to a specific module.
     */
    public function hasAccess(string $module): bool
    {
        // Admin and Owner always have full access
        if ($this->role === 'admin' || $this->role === 'owner') {
            return true;
        }

        // Implicit access based on role for core modules
        $implicitAccess = [
            'cs' => ['cs', 'cs.greeting', 'cs.spk'],
            'gudang' => ['gudang', 'warehouse.storage', 'manifest.index', 'manifest.create', 'manifest.store'],
            'technician' => ['workshop.dashboard', 'preparation', 'sortir', 'production', 'qc', 'gallery'],
            'pic' => ['sortir', 'admin.materials', 'admin.materials.request'],
            'finance' => ['finance'],
            'spv' => ['workshop.dashboard', 'admin.performance', 'admin.reports'],
            'hr' => ['admin.users'],
            'user' => ['profile'], // Generic staff with no special role
        ];

        if (isset($implicitAccess[$this->role]) && in_array($module, $implicitAccess[$this->role])) {
            return true;
        }

        $hasAccess = in_array($module, $this->access_rights ?? []);
        
        if (!$hasAccess && !in_array($this->role, ['admin', 'owner'])) {
             \Illuminate\Support\Facades\Log::debug("User {$this->id} ({$this->name}) role: {$this->role} denied access to module: {$module}. Rights: " . json_encode($this->access_rights));
        }

        // Check if access_rights contains the module
        return $hasAccess;
    }

    // Relationships for WorkOrder tracking
    // Preparation
    public function jobsPrepWashing() { return $this->hasMany(WorkOrder::class, 'prep_washing_by'); }
    public function jobsPrepSol() { return $this->hasMany(WorkOrder::class, 'prep_sol_by'); }
    public function jobsPrepUpper() { return $this->hasMany(WorkOrder::class, 'prep_upper_by'); }

    // Production
    public function jobsProdSol() { return $this->hasMany(WorkOrder::class, 'prod_sol_by'); }
    public function jobsProdUpper() { return $this->hasMany(WorkOrder::class, 'prod_upper_by'); }
    public function jobsProdCleaning() { return $this->hasMany(WorkOrder::class, 'prod_cleaning_by'); }
    
    // Legacy / General Assignment
    public function jobsProduction() { return $this->hasMany(WorkOrder::class, 'technician_production_id'); }
    public function jobsSortirSol() { return $this->hasMany(WorkOrder::class, 'pic_sortir_sol_id'); }
    public function jobsSortirUpper() { return $this->hasMany(WorkOrder::class, 'pic_sortir_upper_id'); }

    // QC
    public function jobsQcJahit() { return $this->hasMany(WorkOrder::class, 'qc_jahit_technician_id'); }
    public function jobsQcCleanup() { return $this->hasMany(WorkOrder::class, 'qc_cleanup_technician_id'); }
    public function jobsQcFinal() { return $this->hasMany(WorkOrder::class, 'qc_final_pic_id'); }
    
    // Relationship specifically for completed QC Final tasks (using timestamp tracking column)
    public function qcFinalCompleted() { return $this->hasMany(WorkOrder::class, 'qc_final_by'); }

    public function logs() { return $this->hasMany(WorkOrderLog::class); }
}
