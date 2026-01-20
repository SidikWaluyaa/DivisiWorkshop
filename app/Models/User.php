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
        // Admin always has access OR if role matches module (legacy support)
        if ($this->role === 'admin') {
            return true;
        }

        // Check if access_rights contains the module
        // We use a simple array check.
        // If module is 'dashboard', checking if 'dashboard' is in array.
        // We also support wildcards or specific logic if needed, but for now exact match.
        // EXCEPT: 'dashboard' is allowed for everyone usually? Or restricting strictly?
        // Let's make it strict based on the array.
        
        return in_array($module, $this->access_rights ?? []);
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

    public function logs() { return $this->hasMany(WorkOrderLog::class); }
}
