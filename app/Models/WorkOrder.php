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
        'prep_washing_started_at',
        'prep_washing_completed_at',
        'prep_washing_by',
        'prep_sol_started_at',
        'prep_sol_completed_at',
        'prep_sol_by',
        'prep_upper_started_at',
        'prep_upper_completed_at',
        'prep_upper_by',
        // Production Tracking
        'prod_sol_started_at',
        'prod_sol_completed_at',
        'prod_sol_by',
        'prod_upper_started_at',
        'prod_upper_completed_at',
        'prod_upper_by',
        'prod_cleaning_started_at',
        'prod_cleaning_completed_at',
        'prod_cleaning_by',
        // QC Tracking
        'qc_jahit_started_at',
        'qc_jahit_completed_at',
        'qc_jahit_by',
        'qc_cleanup_started_at',
        'qc_cleanup_completed_at',
        'qc_cleanup_by',
        'qc_final_started_at',
        'qc_final_completed_at',
        'qc_final_by',
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
        'payment_proof',
        'payment_method',
        'payment_notes',
        'total_transaksi',
        'total_paid',
        'sisa_tagihan',
        'status_pembayaran',
        'category_spk',
        'cs_code',
        'cx_handler_id',
        'workshop_manifest_id',
        'waktu',
        'invoice_token',
        'invoice_awal',
        'invoice_akhir',
        'finish_report_url',
        'late_description',
        'new_estimation_date',
    ];

    public function cxHandler()
    {
        return $this->belongsTo(User::class, 'cx_handler_id');
    }

    protected $casts = [
        'status' => \App\Enums\WorkOrderStatus::class, // Enum Casting
        'entry_date' => 'datetime',
        'estimation_date' => 'datetime',
        'new_estimation_date' => 'datetime',
        'finished_date' => 'datetime',
        'taken_date' => 'datetime',
        'payment_due_date' => 'datetime',
        'last_reminder_at' => 'datetime',
        'donated_at' => 'datetime',
        // Preparation
        'prep_washing_started_at' => 'datetime',
        'prep_washing_completed_at' => 'datetime',
        'prep_sol_started_at' => 'datetime',
        'prep_sol_completed_at' => 'datetime',
        'prep_upper_started_at' => 'datetime',
        'prep_upper_completed_at' => 'datetime',
        // Production
        'prod_sol_started_at' => 'datetime',
        'prod_sol_completed_at' => 'datetime',
        'prod_upper_started_at' => 'datetime',
        'prod_upper_completed_at' => 'datetime',
        'prod_cleaning_started_at' => 'datetime',
        'prod_cleaning_completed_at' => 'datetime',
        // QC
        'qc_jahit_started_at' => 'datetime',
        'qc_jahit_completed_at' => 'datetime',
        'qc_cleanup_started_at' => 'datetime',
        'qc_cleanup_completed_at' => 'datetime',
        'qc_final_started_at' => 'datetime',
        'qc_final_completed_at' => 'datetime',
        'accessories_data' => 'array',
        'reception_qc_passed' => 'boolean',
        'warehouse_qc_at' => 'datetime',
        'finance_entry_at' => 'datetime',
        'finance_exit_at' => 'datetime',
        'phone_normalized' => 'boolean', // Flag for normalization if needed
        'total_transaksi' => 'float',
        'total_paid' => 'float',
        'sisa_tagihan' => 'float',
        'previous_status' => \App\Enums\WorkOrderStatus::class,
        'waktu' => 'datetime',
    ];

    protected $appends = ['spk_cover_photo_url'];

    /**
     * Boot the model - handle cascade deletes
     */
    protected static function boot()
    {
        parent::boot();

        // Auto update 'waktu' and finance data when status changes
        static::updating(function ($model) {
            if ($model->isDirty('status')) {
                $model->waktu = now();
            }

            // ALWAYS refresh finance status on update to prevent NULL values
            $model->recalculateTotalPrice(false);
        });

        // Auto generate invoice_token and URLs for new orders
        static::creating(function ($model) {
            if (!$model->invoice_token) {
                $model->invoice_token = \Illuminate\Support\Str::random(32);
            }

            $baseUrl = config('app.url');
            $model->invoice_awal = $baseUrl . "/api/invoice_share.php?type=awal&token=" . $model->invoice_token;

            // Auto-calculate finance status even if it's SPK Pending
            $model->recalculateTotalPrice(false);

            if ($model->status_pembayaran === 'L') {
                $model->invoice_akhir = $baseUrl . "/api/invoice_share.php?type=akhir&token=" . $model->invoice_token;
            }
        });

        // Update invoice_akhir URL when status becomes 'L'
        static::updating(function ($model) {
            if ($model->isDirty('status_pembayaran') && $model->status_pembayaran === 'L') {
                $baseUrl = config('app.url');
                $model->invoice_akhir = $baseUrl . "/api/invoice_share.php?type=akhir&token=" . $model->invoice_token;
            }
        });

        // When WorkOrder is being deleted (soft or force), clean up storage assignments
        static::deleting(function ($workOrder) {
            // Get all storage assignments for this work order
            $assignments = \App\Models\StorageAssignment::where('work_order_id', $workOrder->id)
                ->whereNull('retrieved_at')
                ->get();

            foreach ($assignments as $assignment) {
                // Decrement rack count
                $rack = \App\Models\StorageRack::where('rack_code', $assignment->rack_code)->first();
                if ($rack && $rack->current_count > 0) {
                    $rack->decrement('current_count');
                }
            }

            // Delete all storage assignments for this work order
            \App\Models\StorageAssignment::where('work_order_id', $workOrder->id)->delete();
        });
    }

    /**
     * Normalize customer phone number before saving
     */
    public function setCustomerPhoneAttribute($value)
    {
        $this->attributes['customer_phone'] = \App\Helpers\PhoneHelper::normalize($value);
    }

    // ========================================
    // Service Category Helpers (Unified)
    // ========================================
    public function scopeWithServiceCategory($query, $categoryNames)
    {
        $categories = (array) $categoryNames;
        return $query->whereHas('workOrderServices', function ($q) use ($categories) {
            $q->where(function ($sq) use ($categories) {
                foreach ($categories as $cat) {
                    $sq->orWhere('category_name', 'like', "%$cat%")
                        ->orWhere('custom_service_name', 'like', "%$cat%")
                        ->orWhereHas('service', function ($ssq) use ($cat) {
                            $ssq->where('category', 'like', "%$cat%")
                                ->orWhere('name', 'like', "%$cat%");
                        });
                }
            });
        });
    }

    public function scopeWithoutServiceCategory($query, $categoryNames)
    {
        $categories = (array) $categoryNames;
        return $query->whereDoesntHave('workOrderServices', function ($q) use ($categories) {
            $q->where(function ($sq) use ($categories) {
                foreach ($categories as $cat) {
                    $sq->orWhere('category_name', 'like', "%$cat%")
                        ->orWhere('custom_service_name', 'like', "%$cat%")
                        ->orWhereHas('service', function ($ssq) use ($cat) {
                            $ssq->where('category', 'like', "%$cat%")
                                ->orWhere('name', 'like', "%$cat%");
                        });
                }
            });
        });
    }

    // Instance Level Helpers
    public function hasServiceCategory($categoryNames)
    {
        $categories = (array) $categoryNames;
        return $this->workOrderServices()->where(function ($q) use ($categories) {
            foreach ($categories as $cat) {
                $q->orWhere('category_name', 'like', "%$cat%")
                    ->orWhere('custom_service_name', 'like', "%$cat%")
                    ->orWhereHas('service', function ($sq) use ($cat) {
                        $sq->where('category', 'like', "%$cat%")
                            ->orWhere('name', 'like', "%$cat%");
                    });
            }
        })->exists();
    }

    // ========================================
    // Production Scopes (Queue Filtering)
    // ========================================
    public function scopeProductionSol($query)
    {
        return $query->withServiceCategory(['Sol']);
    }

    public function scopeProductionUpper($query)
    {
        return $query->withServiceCategory(['Upper', 'Repaint', 'Jahit'])
            ->where(function ($q) {
                // Show if Sol NOT required OR Sol Completed
                $q->withoutServiceCategory(['Sol'])
                    ->orWhereNotNull('prod_sol_completed_at');
            });
    }

    public function scopeProductionTreatment($query)
    {
        return $query->withServiceCategory(['Cleaning', 'Whitening', 'Repaint', 'Treatment', 'Cuci'])
            ->where(function ($q) {
                // Sol Condition
                $q->withoutServiceCategory(['Sol'])
                    ->orWhereNotNull('prod_sol_completed_at');
            })
            ->where(function ($q) {
                // Upper Condition
                $q->withoutServiceCategory(['Upper', 'Repaint', 'Jahit'])
                    ->orWhereNotNull('prod_upper_completed_at');
            });
    }

    public function scopeProductionLate($query)
    {
        return $query->where('status', \App\Enums\WorkOrderStatus::PRODUCTION->value)
            ->select('*')
            ->selectRaw("
                DATEDIFF(estimation_date, NOW()) as calendar_days_remaining,
                CASE 
                    WHEN DATEDIFF(estimation_date, NOW()) < 0 THEN 1
                    WHEN DATEDIFF(estimation_date, NOW()) <= 5 THEN 2
                    ELSE 3
                END as priority_scale,
                CASE 
                    WHEN DATEDIFF(estimation_date, NOW()) < 0 THEN 'LATE'
                    WHEN DATEDIFF(estimation_date, NOW()) <= 5 THEN 'WARNING'
                    ELSE 'ON TRACK'
                END as warning_status
            ")
            ->orderBy('priority_scale', 'asc')
            ->orderBy('calendar_days_remaining', 'asc');
    }

    // ========================================
    // QC SCOPES ===

    public function scopeQcJahit($query)
    {
        return $query->withServiceCategory(['Sol', 'Upper', 'Repaint', 'Jahit']);
    }

    public function scopeQcCleanup($query)
    {
        return $query->where(function ($q) {
            $q->withoutServiceCategory(['Sol', 'Upper', 'Repaint', 'Jahit'])
                ->orWhereNotNull('qc_jahit_completed_at');
        });
    }

    public function scopeQcFinal($query)
    {
        // Show if Cleanup is DONE
        // AND Final NOT done
        return $query->whereNotNull('qc_cleanup_completed_at');
    }

    public function scopeQcReview($query)
    {
        // Ready for Admin Review (All QCs done)
        return $query->whereNotNull('qc_cleanup_completed_at')
            ->whereNotNull('qc_final_completed_at')
            ->where(function ($q) {
                $q->whereDoesntHave(
                    'services',
                    fn($s) =>
                    $s->where('category', 'like', '%Sol%')->orWhere('name', 'like', '%Sol%')
                        ->orWhere('category', 'like', '%Upper%')->orWhere('name', 'like', '%Upper%')
                        ->orWhere('category', 'like', '%Repaint%')->orWhere('name', 'like', '%Repaint%')
                )->orWhereNotNull('qc_jahit_completed_at');
            });
    }


    public function prodSolBy()
    {
        return $this->belongsTo(User::class, 'prod_sol_by');
    }
    public function prodUpperBy()
    {
        return $this->belongsTo(User::class, 'prod_upper_by');
    }
    public function prodCleaningBy()
    {
        return $this->belongsTo(User::class, 'prod_cleaning_by');
    }

    public function qcJahitBy()
    {
        return $this->belongsTo(User::class, 'qc_jahit_by');
    }
    public function qcCleanupBy()
    {
        return $this->belongsTo(User::class, 'qc_cleanup_by');
    }
    public function qcFinalBy()
    {
        return $this->belongsTo(User::class, 'qc_final_by');
    }




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
        return $this->hasServiceCategory(['Sol']);
    }

    public function getNeedsUpperAttribute(): bool
    {
        return $this->hasServiceCategory(['Upper', 'Repaint', 'Jahit']);
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
        $doneSol = !$this->needs_sol || !is_null($this->prod_sol_completed_at);

        // 2. Upper
        $doneUpper = !$this->needs_upper || !is_null($this->prod_upper_completed_at);

        // 3. Treatment / Cleaning / Repaint
        $needsTreatment = $this->hasServiceCategory(['Cleaning', 'Whitening', 'Repaint', 'Treatment', 'Cuci']);
        $doneTreatment = !$needsTreatment || !is_null($this->prod_cleaning_completed_at);

        return $doneSol && $doneUpper && $doneTreatment;
    }

    public function getIsQcFinishedAttribute(): bool
    {
        // 1. QC Jahit (Only if needed)
        $needsJahit = $this->hasServiceCategory(['Sol', 'Upper', 'Repaint', 'Jahit']);
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
            return !$this->materials->contains(function ($m) {
                return $m->pivot->status === 'REQUESTED';
            });
        }

        return $this->materials()->wherePivot('status', 'REQUESTED')->count() === 0;
    }

    public function getTotalPriceAttribute()
    {
        return $this->workOrderServices()->sum('cost');
    }

    /**
     * Calculate the total service price without saving
     */
    public function calculateTotalPrice()
    {
        return $this->workOrderServices()->sum('cost');
    }

    /**
     * Recalculate and save all price-related fields
     * Updates: total_service_price, total_transaksi, sisa_tagihan, status_pembayaran, invoice_akhir
     */
    public function recalculateTotalPrice($save = true)
    {
        // 1. Calculate Transaction Total
        $jasa = $this->workOrderServices()->sum('cost');
        $oto = $this->cost_oto ?? 0;
        $add = $this->cost_add_service ?? 0;
        $ongkir = $this->shipping_cost ?? 0;
        $discount = $this->discount ?? 0;
        $uniqueCode = $this->unique_code ?? 0;

        $totalTransaksi = ($jasa + $oto + $add + $ongkir + $uniqueCode) - $discount;
        if ($totalTransaksi < 0)
            $totalTransaksi = 0;

        // 2. FRESH Query for payments to avoid stale collection data (CRITICAL FIX)
        $paid = $this->payments()->sum('amount_total');
        $sisa = $totalTransaksi - $paid;

        // 3. Determine Payment Status
        if ($sisa <= 0 && $totalTransaksi > 0) {
            $statusPembayaran = 'L';
        } elseif ($paid > 0) {
            $statusPembayaran = 'DP/Cicil';
        } else {
            $statusPembayaran = 'Belum Bayar';
        }

        // 4. Handle invoice_akhir URL automation
        $invoiceAkhir = $this->invoice_akhir;
        if ($statusPembayaran === 'L' && empty($invoiceAkhir)) {
            $baseUrl = config('app.url');
            $invoiceAkhir = $baseUrl . "/api/invoice_share.php?type=akhir&token=" . $this->invoice_token;
        }

        // 5. Parse SPK for info
        $parsed = $this->parseSpkInfo();

        // 6. Update Database or Fill Attributes
        $data = [
            'total_service_price' => $jasa,
            'total_transaksi' => $totalTransaksi,
            'total_paid' => $paid,
            'sisa_tagihan' => $sisa,
            'status_pembayaran' => $statusPembayaran,
            'invoice_akhir' => $invoiceAkhir,
            'category_spk' => $parsed['category'],
            'cs_code' => $parsed['cs_code'],
        ];

        if ($save) {
            $this->update($data);
        } else {
            $this->fill($data);
        }

        return $jasa;
    }

    /**
     * Parse SPK number to extract Category and CS Code
     */
    public function parseSpkInfo()
    {
        $spk = $this->spk_number;
        if (!$spk)
            return ['category' => '-', 'cs_code' => '-'];

        $parts = explode('-', $spk);

        // Category Map (Legacy mapping from FinanceController)
        $catMap = [
            'N' => 'Online',
            'P' => 'Pickup',
            'J' => 'Ojol',
            'F' => 'Offline',
            'S' => 'Sepatu',
            'T' => 'Tas',
            'H' => 'Headwear',
            'A' => 'Apparel',
            'L' => 'Lainnya'
        ];

        $category = isset($parts[0]) ? ($catMap[strtoupper($parts[0])] ?? $parts[0]) : '-';
        $cs_code = (count($parts) >= 5) ? strtoupper($parts[count($parts) - 1]) : '-';

        return [
            'category' => $category,
            'cs_code' => $cs_code
        ];
    }

    // Relationships for Technicians/PICs
    public function picSortirSol()
    {
        return $this->belongsTo(User::class, 'pic_sortir_sol_id');
    }
    public function picSortirUpper()
    {
        return $this->belongsTo(User::class, 'pic_sortir_upper_id');
    }
    public function technicianProduction()
    {
        return $this->belongsTo(User::class, 'technician_production_id');
    }
    public function qcJahitTechnician()
    {
        return $this->belongsTo(User::class, 'qc_jahit_technician_id');
    }
    public function qcCleanupTechnician()
    {
        return $this->belongsTo(User::class, 'qc_cleanup_technician_id');
    }
    public function qcFinalPic()
    {
        return $this->belongsTo(User::class, 'qc_final_pic_id');
    }

    // Preparation Technicians
    public function prepWashingBy()
    {
        return $this->belongsTo(User::class, 'prep_washing_by');
    }
    public function prepSolBy()
    {
        return $this->belongsTo(User::class, 'prep_sol_by');
    }
    public function prepUpperBy()
    {
        return $this->belongsTo(User::class, 'prep_upper_by');
    }

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

    public function workshopManifest()
    {
        return $this->belongsTo(WorkshopManifest::class);
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

        // 2. Fallback: Reception Photo (Foto Referensi) or Warehouse Before
        $reception = $this->photos()->whereIn('step', ['RECEPTION', 'WAREHOUSE_BEFORE'])->first();
        if ($reception) {
            return $reception->file_path;
        }

        // 3. Last Fallback: First available photo
        $first = $this->photos()->first();
        return $first ? $first->file_path : null;
    }

    /**
     * Get the full URL for the SPK Cover Photo
     */
    public function getSpkCoverPhotoUrlAttribute()
    {
        $path = $this->spk_cover_photo;

        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    /**
     * Generate unique code between 100-999 if not exists
     * Ensures uniqueness among ACTIVE (Unpaid/Partial) transactions only.
     * Maximum 999 - NEVER exceeds 3 digit.
     */
    public function ensureUniqueCode()
    {
        if ($this->unique_code) {
            return $this->unique_code;
        }

        // Helper: check if code is already used by active transactions
        $isCodeUsed = function ($code) {
            return static::where('unique_code', $code)
                ->where('id', '!=', $this->id)
                ->where(function ($q) {
                    $q->whereNull('status_pembayaran')
                        ->orWhere('status_pembayaran', '!=', 'L'); // Hanya yang belum lunas
                })
                ->exists();
        };

        // Step 1: Random attempts (fast path)
        $maxAttempts = 50;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = rand(100, 999);
            if (!$isCodeUsed($code)) {
                $this->unique_code = $code;
                $this->save();
                return $code;
            }
        }

        // Step 2: Sequential scan (guaranteed find if any slot available)
        // Ambil semua kode unik yang sedang aktif
        $usedCodes = static::where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->whereNull('status_pembayaran')
                    ->orWhere('status_pembayaran', '!=', 'L');
            })
            ->whereNotNull('unique_code')
            ->pluck('unique_code')
            ->toArray();

        // Cari slot kosong dari 100-999
        $allCodes = range(100, 999);
        $availableCodes = array_diff($allCodes, $usedCodes);

        if (!empty($availableCodes)) {
            // Pilih random dari yang tersedia
            $code = $availableCodes[array_rand($availableCodes)];
            $this->unique_code = $code;
            $this->save();
            return $code;
        }

        // Step 3: Semua 900 slot penuh (sangat jarang terjadi)
        // Reset kode unik transaksi LUNAS yang paling lama, lalu pakai kodenya
        $oldestPaid = static::where('status_pembayaran', 'L')
            ->whereNotNull('unique_code')
            ->orderBy('updated_at', 'asc')
            ->first();

        if ($oldestPaid) {
            $code = $oldestPaid->unique_code;
            $oldestPaid->update(['unique_code' => null]); // Bebaskan kode
            $this->unique_code = $code;
            $this->save();
            return $code;
        }

        // Final fallback: pakai random (duplicate, tapi ini benar-benar edge case)
        $this->unique_code = rand(100, 999);
        $this->save();
        return $this->unique_code;
    }
    /**
     * Generate unique SPK number based on formula:
     * [ItemType]-[YearMonth]-[Date]-[Sequence]-[CSCode]
     * Example: S-2501-31-0001-QA
     */
    public static function generateSpkNumber($itemType = 'Sepatu', $csCode = 'SW')
    {
        $typeCodes = [
            'Sepatu' => 'S',
            'Tas' => 'T',
            'Headwear' => 'H', // Topi, Helm
            'Apparel' => 'A', // Jaket, Baju
            'Lainnya' => 'L',
        ];

        $code = $typeCodes[$itemType] ?? 'S'; // Default to Sepatu
        $yearMonth = date('ym');
        $day = date('d');

        // Search Pattern for THIS MONTH: S-2501-%
        $prefixPattern = "{$code}-{$yearMonth}-";

        // Find max sequence specifically for THIS MONTH
        $maxSequence = 0;

        $workOrders = \App\Models\WorkOrder::withTrashed()->where('spk_number', 'like', $prefixPattern . '%')
            ->select('spk_number')
            ->get();

        foreach ($workOrders as $wo) {
            $parts = explode('-', $wo->spk_number);
            // Expected parts: [Type, YM, D, Seq, CS]
            if (count($parts) >= 4 && is_numeric($parts[3])) {
                $maxSequence = max($maxSequence, (int) $parts[3]);
            }
        }

        $nextSequence = str_pad($maxSequence + 1, 4, '0', STR_PAD_LEFT);

        // Ensure CS Code is sanitized
        $csCode = strtoupper($csCode ?: 'SW');

        return "{$code}-{$yearMonth}-{$day}-{$nextSequence}-{$csCode}";
    }
}
