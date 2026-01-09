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
        ];
    }

    // Performance Relationships
    public function jobsSortirSol() { return $this->hasMany(WorkOrder::class, 'pic_sortir_sol_id'); }
    public function jobsSortirUpper() { return $this->hasMany(WorkOrder::class, 'pic_sortir_upper_id'); }
    public function jobsProduction() { return $this->hasMany(WorkOrder::class, 'technician_production_id'); }
    public function jobsQcJahit() { return $this->hasMany(WorkOrder::class, 'qc_jahit_technician_id'); }
    public function jobsQcCleanup() { return $this->hasMany(WorkOrder::class, 'qc_cleanup_technician_id'); }
    public function jobsQcFinal() { return $this->hasMany(WorkOrder::class, 'qc_final_pic_id'); }
    
    public function logs() { return $this->hasMany(WorkOrderLog::class); }
}
