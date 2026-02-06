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
        if ($this->isAdmin() || $this->isOwner()) {
            return true;
        }

        // Standardized 5 Pillar Architecture
        $implicitAccess = [
            'cs' => ['cs', 'cs.greeting', 'cs.spk', 'cs.analytics'],
            'gudang' => ['gudang', 'warehouse.storage', 'manifest.index', 'material.requests', 'storage.dashboard'],
            'workshop' => ['workshop', 'workshop.dashboard', 'assessment', 'preparation', 'sortir', 'production', 'qc', 'finish', 'gallery'],
            'finance' => ['finance'],
            'cx' => ['cx', 'cx.dashboard', 'cx.oto', 'complaints'],
            'hr' => ['hr', 'admin.users'],
        ];

        if (isset($implicitAccess[$this->role]) && in_array($module, $implicitAccess[$this->role])) {
            return true;
        }

        // Custom access via access_rights JSON field
        $hasAccess = in_array($module, $this->access_rights ?? []);
        
        if (!$hasAccess) {
             \Illuminate\Support\Facades\Log::debug("User {$this->id} ({$this->name}) role: {$this->role} denied access to module: {$module}.");
        }

        return $hasAccess;
    }

    // Role Helper Methods
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isCS(): bool { return $this->role === 'cs'; }
    public function isGudang(): bool { return $this->role === 'gudang'; }
    public function isWorkshop(): bool { return $this->role === 'workshop' || $this->role === 'technician'; }
    public function isFinance(): bool { return $this->role === 'finance'; }
    public function isCX(): bool { return $this->role === 'cx'; }

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
