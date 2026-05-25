<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'total_amount',
        'paid_amount',
        'discount',
        'shipping_cost',
        'status', // Belum Bayar, DP/Cicil, Lunas
        'due_date',
        'estimasi_selesai',
        'notes',
        'invoice_awal_url',
        'invoice_akhir_url',
        'target_dp_amount',
        'dp_unique_code',
        'final_unique_code',
        'invoice_dp_url',
        'invoice_final_url',
        'invoice_full_url',
        'is_manual_estimasi',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'estimasi_selesai' => 'datetime',
        'target_dp_amount' => 'decimal:2',
        'dp_unique_code' => 'integer',
        'final_unique_code' => 'integer',
        'total_dp_with_code' => 'decimal:2',
        'total_pelunasan_with_code' => 'decimal:2',
        'is_manual_estimasi' => 'boolean',
    ];

    /**
     * Get the customer that owns the invoice.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the work orders included in this invoice.
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    /**
     * Get the payments associated with this invoice.
     */
    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    /**
     * Get the manual invoice payments (for verification system).
     */
    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    /**
     * Get payment status code: BB (Belum Bayar), BL (Belum Lunas), L (Lunas).
     */
    public function getPaymentStatusCodeAttribute(): string
    {
        if ($this->paid_amount <= 0) return 'BB';
        if ($this->paid_amount < ($this->total_amount + $this->shipping_cost - $this->discount)) return 'BL';
        return 'L';
    }

    /**
     * Calculated remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount + $this->shipping_cost - $this->paid_amount - $this->discount;
    }

    /**
     * Semantic accessor for Total DP (including unique code)
     */
    public function getTotalDpAttribute()
    {
        return $this->total_dp_with_code;
    }

    /**
     * Semantic accessor for Total Pelunasan (including unique code)
     * Returns 0 if status is Lunas.
     */
    public function getTotalPelunasanAttribute()
    {
        if ($this->status === 'Lunas') return 0;
        return $this->total_pelunasan_with_code;
    }

    /**
     * Semantic accessor for Total Full Payment (100% including unique code)
     */
    public function getTotalFullAttribute()
    {
        return ($this->total_amount + $this->shipping_cost - $this->discount) + ($this->final_unique_code ?: 0);
    }

    /**
     * Synchronize total amounts from associated work orders.
     */
    public function syncFinancials()
    {
        $totals = $this->workOrders()
            ->selectRaw('
                COALESCE(SUM(total_transaksi), 0) as total_amount
            ')
            ->first();

        // Calculate paid amount from both Invoice level and associated WorkOrders
        // PERMINTAAN USER: Hapus syarat verifikasi mutasi. Semua pembayaran yang diinput langsung masuk hitungan Lunas.
        $invoicePaid = $this->payments()->sum('amount_total');
        
        $spkIds = $this->workOrders()->pluck('id');
        $spkPaid = OrderPayment::whereIn('work_order_id', $spkIds)
            ->whereNull('invoice_id') // Avoid double counting if a payment is linked to both
            ->sum('amount_total');

        $totalPaid = $invoicePaid + $spkPaid;

        $this->total_amount = $totals->total_amount;
        $this->paid_amount = $totalPaid;

        // DP Target = 70% of Total Amount
        $this->target_dp_amount = 0.70 * $this->total_amount;

        $remaining = $this->remaining_balance;

        // Status Logic
        if ($remaining <= 0 && $this->total_amount > 0) {
            $this->status = 'Lunas';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'DP/Cicil';
        } else {
            $this->status = 'Belum Bayar';
        }

        // --- SLA SINKRONISASI & ESTIMASI SELESAI BERBASIS PEMBAYARAN ---
        if ($this->is_manual_estimasi) {
            // SKIP auto-SLA estimation calculations to protect manual overrides
        } else {
            if ($this->status === 'Belum Bayar') {
                $this->estimasi_selesai = null;
                // Kosongkan estimasi selesai untuk seluruh work order (SPK) terkait yang tidak manual
                foreach ($this->workOrders as $workOrder) {
                    if (!$workOrder->is_manual_estimasi && $workOrder->estimation_date !== null) {
                        $workOrder->estimation_date = null;
                        $workOrder->saveQuietly();
                    }
                }
            } else {
                // Status is DP/Cicil or Lunas: hitung SLA berbasis pembayaran pertama
                $spkIds = $this->workOrders()->pluck('id')->toArray();
                
                // Ambil tanggal pembayaran terawal dari OrderPayment
                $earliestOrderPayment = \App\Models\OrderPayment::where(function($q) use ($spkIds) {
                        $q->where('invoice_id', $this->id)
                          ->orWhereIn('work_order_id', $spkIds);
                    })
                    ->min('paid_at');

                // Ambil tanggal pembayaran terawal dari InvoicePayment
                $earliestInvoicePayment = \App\Models\InvoicePayment::where('invoice_id', $this->id)->min('payment_date');

                $basisDate = null;
                if ($earliestOrderPayment && $earliestInvoicePayment) {
                    $earliestOrderPayment = \Carbon\Carbon::parse($earliestOrderPayment);
                    $earliestInvoicePayment = \Carbon\Carbon::parse($earliestInvoicePayment);
                    $basisDate = $earliestOrderPayment->lt($earliestInvoicePayment) ? $earliestOrderPayment : $earliestInvoicePayment;
                } elseif ($earliestOrderPayment) {
                    $basisDate = \Carbon\Carbon::parse($earliestOrderPayment);
                } elseif ($earliestInvoicePayment) {
                    $basisDate = \Carbon\Carbon::parse($earliestInvoicePayment);
                } else {
                    // Fallback menggunakan waktu sekarang jika belum ada catatan pembayaran
                    $basisDate = now();
                }

                // Cari nilai hk_days tertinggi untuk Invoice SLA
                $maxHkDays = $this->workOrders()->whereNotNull('hk_days')->max('hk_days');

                if ($maxHkDays !== null) {
                    $this->estimasi_selesai = self::addWorkingDays($basisDate, (int) $maxHkDays);
                } else {
                    $this->estimasi_selesai = null;
                }

                // Sinkronkan masing-masing work order (SPK) yang tidak manual
                foreach ($this->workOrders as $workOrder) {
                    if ($workOrder->is_manual_estimasi) {
                        continue;
                    }
                    if ($workOrder->hk_days !== null) {
                        $newEstDate = self::addWorkingDays($basisDate, (int) $workOrder->hk_days);
                        if ($workOrder->estimation_date === null || !$workOrder->estimation_date->eq($newEstDate)) {
                            $workOrder->estimation_date = $newEstDate;
                            $workOrder->saveQuietly();
                        }
                    } else {
                        if ($workOrder->estimation_date !== null) {
                            $workOrder->estimation_date = null;
                            $workOrder->saveQuietly();
                        }
                    }
                }
            }
        }

        // UNIQUE CODE LOGIC (Smart Picker)
        if (!$this->dp_unique_code) {
            $this->dp_unique_code = $this->pickAvailableUniqueCode();
        }
        if (!$this->final_unique_code) {
            $this->final_unique_code = $this->pickAvailableUniqueCode([$this->dp_unique_code]);
        }

        // URL Generation Logic
        $baseUrl = url('/');
        $token = urlencode($this->invoice_number);
        
        // Legacy/Generic URLs (Keep for backward compatibility)
        $this->invoice_awal_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=BL';
        if ($this->status === 'DP/Cicil' || $this->status === 'Lunas') {
            $this->invoice_akhir_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=L';
        }

        // New Specific Penagihan URLs
        $this->invoice_dp_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=DP';
        $this->invoice_final_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=FP';
        $this->invoice_full_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=FULL';

        // Manual sync for dynamic columns (to support older DB versions)
        $this->total_dp_with_code = $this->target_dp_amount + ($this->dp_unique_code ?: 0);
        $this->total_pelunasan_with_code = $this->remaining_balance + ($this->final_unique_code ?: 0);

        $this->save();
    }

    /**
     * Pick a random code between 1-500 that is not currently in use by active invoices.
     */
    private function pickAvailableUniqueCode(array $exclude = [])
    {
        // Get all codes currently in use by unpaid/active invoices
        $usedCodes = self::whereIn('status', ['Belum Bayar', 'DP/Cicil'])
            ->where('id', '!=', $this->id)
            ->pluck('dp_unique_code')
            ->merge(
                self::whereIn('status', ['Belum Bayar', 'DP/Cicil'])
                ->where('id', '!=', $this->id)
                ->pluck('final_unique_code')
            )
            ->merge($exclude)
            ->filter()
            ->unique()
            ->toArray();

        // If for some reason all 500 codes are taken (very rare), just return a random one
        if (count($usedCodes) >= 500) {
            return rand(1, 500);
        }

        // Find available codes
        $availableCodes = array_diff(range(1, 500), $usedCodes);
        
        // Pick a random one from available
        return $availableCodes[array_rand($availableCodes)];
    }

    /**
     * Synchronize the overall SPK completion status based on associated work orders.
     */
    public function syncSpkStatus()
    {
        // Check if there's any work order that is NOT "SELESAI"
        $hasUnfinished = $this->workOrders()->where('status', '!=', 'SELESAI')->exists();
        
        $this->spk_status = $hasUnfinished ? 'BELUM SELESAI' : 'SELESAI';
        $this->save();
    }

    /**
     * Check if the DP (70%) target has been met.
     */
    public function getIsDpPaidAttribute()
    {
        if ($this->target_dp_amount <= 0) return false;
        return $this->paid_amount >= $this->target_dp_amount;
    }

    /**
     * Helper to add working days, skipping Sundays.
     */
    public static function addWorkingDays(\Carbon\Carbon $date, int $days): \Carbon\Carbon
    {
        $tempDate = $date->copy();
        while ($days > 0) {
            $tempDate->addDay();
            if ($tempDate->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                $days--;
            }
        }
        return $tempDate;
    }
}
