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
        'notes',
        'invoice_awal_url',
        'invoice_akhir_url',
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
     * Calculated remaining balance
     */
    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount + $this->shipping_cost - $this->paid_amount - $this->discount;
    }

    /**
     * Synchronize total amounts from associated work orders.
     */
    public function syncFinancials()
    {
        $totals = $this->workOrders()
            ->selectRaw('
                COALESCE(SUM(total_transaksi), 0) as total_amount,
                COALESCE(SUM(total_paid), 0) as paid_amount
            ')
            ->first();

        // WorkOrder `total_transaksi` already accounts for individual SPK discounts.
        // We only sum up the final transaction total and how much is paid.
        $this->total_amount = $totals->total_amount;
        $this->paid_amount = $totals->paid_amount;

        $remaining = $this->remaining_balance;

        if ($remaining <= 0 && $this->total_amount > 0) {
            $this->status = 'Lunas';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'DP/Cicil';
        } else {
            $this->status = 'Belum Bayar';
        }

        // URL Generation Logic based on Status (Secure Token-based)
    $baseUrl = url('/');
    $token = urlencode($this->invoice_number);
    
    if ($this->status === 'Belum Bayar') {
        $this->invoice_awal_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=BL';
    } elseif ($this->status === 'DP/Cicil' || $this->status === 'Lunas') {
        // Generate atau perbarui link awal
        $this->invoice_awal_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=BL';
        // Generate link akhir
        $this->invoice_akhir_url = $baseUrl . '/api/invoice_share_grouped.php?token=' . $token . '&type=L';
    }
        $this->save();
    }
}
