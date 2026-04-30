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
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'estimasi_selesai' => 'datetime',
        'target_dp_amount' => 'decimal:2',
        'dp_unique_code' => 'integer',
        'final_unique_code' => 'integer',
        'total_dp_with_code' => 'decimal:2',
        'total_pelunasan_with_code' => 'decimal:2',
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
}
