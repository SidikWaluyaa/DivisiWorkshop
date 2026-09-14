<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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
        'is_active',
        'specialization',
        'station',
        'workshop_pool',
        'availability_status',
        'is_support',
        'access_rights',
        'cs_code',
        'password',
        'last_active_at',
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
            'is_active' => 'boolean',
            'is_support' => 'boolean',
            'last_active_at' => 'datetime',
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

        // Standardized 7 Pillar Architecture (Implicit default access for primary roles)
        $implicitAccess = [
            'cs' => [
                'dashboard',
                'internal-tracking',
                'cs',
                'cs.dashboard',
                'cs.analytics',
                'cs.leads.konsultasi',
                'cs.leads.follow-up',
                'cs.leads.closing',
                'cs.leads.followup-closing',
                'cs.pending-monitoring',
                'cs.spk',
                'cs.greeting',
                'cs.after-photos',
                'cs.forecasting',
            ],
            'gudang' => [
                'dashboard',
                'internal-tracking',
                'gudang',
                'gudang.outbound-receipt',
                'storage.purchase',
                'storage.disbursement',
                'storage.history',
                'storage.dashboard',
                'warehouse.storage',
                'storage.pickup-history',
                'reception',
                'assessment',
                'manifest.index',
                'storage.manual',
                'storage.manual.racks',
                'finish',
                'shipping',
                'admin.custom-label',
                'material.requests',
                'material-requests',
                'admin.materials.request',
            ],
            'workshop' => [
                'dashboard',
                'internal-tracking',
                'internal-tracking.services',
                'workshop',
                'workshop.dashboard',
                'assessment',
                'preparation',
                'sortir',
                'production',
                'qc',
                'surat-jalan',
                'gallery',
            ],
            'finance' => [
                'dashboard',
                'internal-tracking',
                'finance',
                'finance.dashboard',
                'finance.waiting-payment',
                'finance.transaction',
                'finance.index',
                'finance.invoices',
                'finance.cancelled',
                'finance.cs-verification',
                'finance.payments',
                'finance.mutations',
                'finance.verifications',
                'manifest.index',
            ],
            'cx' => [
                'dashboard',
                'internal-tracking',
                'cx',
                'cx.dashboard',
                'cx.index',
                'cx.history',
                'cx.oto',
                'cx.after-confirmation',
                'cx.shipping-monitoring',
                'cx.overdue',
                'cx.warranty-claims',
                'cx.verified-addresses',
                'admin.complaints',
                'complaints',
            ],
            'spv' => [
                'dashboard',
                'internal-tracking',
                'internal-tracking.services',
                'admin.performance',
                'admin.reports',
                'cs',
                'cs.dashboard',
                'gudang',
                'storage.dashboard',
                'workshop',
                'workshop.dashboard',
                'finance',
                'finance.dashboard',
                'cx',
                'cx.dashboard',
            ],
            'hr' => [
                'dashboard',
                'admin.users',
                'admin.reports',
                'admin.performance',
            ],
            'technician' => [
                'workshop',
                'workshop.dashboard',
                'assessment',
                'preparation',
                'sortir',
                'production',
                'qc',
                'gallery',
            ],
            'pic' => [
                'workshop',
                'workshop.dashboard',
                'sortir',
                'admin.materials',
            ],
            'user' => [
                'dashboard',
                'internal-tracking',
            ],
        ];

        if (isset($implicitAccess[$this->role]) && in_array($module, $implicitAccess[$this->role])) {
            return true;
        }

        // Custom access via access_rights JSON field
        $userRights = is_array($this->access_rights) ? $this->access_rights : (json_decode($this->access_rights, true) ?? []);
        
        if (in_array($module, $userRights)) {
            return true;
        }

        // Module Aliases / Parent Group fallbacks (e.g. if user has 'reception', but middleware/gate checks 'gudang')
        $parentDivisions = [
            'gudang' => [
                'gudang.outbound-receipt', 'storage.purchase', 'storage.disbursement', 'storage.history',
                'storage.dashboard', 'warehouse.storage', 'storage.pickup-history', 'reception',
                'assessment', 'manifest.index', 'storage.manual', 'storage.manual.racks', 'finish',
                'shipping', 'admin.custom-label', 'material-requests', 'admin.materials.request'
            ],
            'cs' => [
                'cs.dashboard', 'cs.analytics', 'cs.leads.konsultasi', 'cs.leads.follow-up',
                'cs.leads.closing', 'cs.leads.followup-closing', 'cs.pending-monitoring', 'cs.spk',
                'cs.greeting', 'cs.after-photos', 'cs.forecasting'
            ],
            'finance' => [
                'finance.dashboard', 'finance.waiting-payment', 'finance.transaction', 'finance.invoices',
                'finance.cancelled', 'finance.cs-verification', 'finance.payments', 'finance.mutations',
                'finance.verifications'
            ],
            'cx' => [
                'cx.dashboard', 'cx.index', 'cx.history', 'cx.oto', 'cx.after-confirmation',
                'cx.shipping-monitoring', 'cx.overdue', 'cx.warranty-claims', 'cx.verified-addresses',
                'admin.complaints', 'complaints'
            ],
            'master' => [
                'admin.customers', 'admin.promotions', 'admin.announcements', 'admin.users',
                'admin.activity-logs', 'admin.reports', 'admin.performance', 'admin.supply-chain',
                'material-requests', 'admin.purchases', 'admin.data-integrity', 'admin.services', 'admin.materials'
            ],
        ];

        // If module requested is a parent division, check if user has at least one submodule in it
        if (isset($parentDivisions[$module])) {
            foreach ($parentDivisions[$module] as $subMod) {
                if (in_array($subMod, $userRights)) {
                    return true;
                }
            }
        }

        // Submodule alias mapping
        $aliasMap = [
            'cs.leads.konsultasi' => ['cs'],
            'cs.leads.follow-up' => ['cs'],
            'cs.leads.closing' => ['cs'],
            'cs.leads.followup-closing' => ['cs'],
            'cs.pending-monitoring' => ['cs', 'cs.spk'],
            'cs.after-photos' => ['cs'],
            'cs.forecasting' => ['cs'],
            'finance.transaction' => ['finance'],
            'finance' => ['finance.transaction', 'finance.dashboard'],
            'cx.followup' => ['cx', 'cx.index'],
            'cx.overdue' => ['cx.overdue-dashboard'],
            'material-requests' => ['admin.materials.request', 'material.requests'],
            'admin.materials.request' => ['material-requests'],
        ];

        if (isset($aliasMap[$module])) {
            foreach ($aliasMap[$module] as $alt) {
                if (in_array($alt, $userRights)) {
                    return true;
                }
            }
        }

        return false;
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
    
    // Relationship specifically for completed QC tasks (using timestamp tracking column)
    public function qcJahitCompleted() { return $this->hasMany(WorkOrder::class, 'qc_jahit_by'); }
    public function qcCleanupCompleted() { return $this->hasMany(WorkOrder::class, 'qc_cleanup_by'); }
    public function qcFinalCompleted() { return $this->hasMany(WorkOrder::class, 'qc_final_by'); }

    public function logs() { return $this->hasMany(WorkOrderLog::class); }

    public function warrantiesCreated()
    {
        return $this->hasMany(WorkOrderWarranty::class, 'created_by');
    }

    public function warrantiesFinished()
    {
        return $this->hasMany(WorkOrderWarranty::class, 'finished_by');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'technician_services')->withTimestamps();
    }
}
