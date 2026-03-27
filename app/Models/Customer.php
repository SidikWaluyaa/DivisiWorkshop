<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    // Properti publik untuk menampung data sementara agar tidak dianggap kolom database oleh Eloquent
    public $old_phone_for_cascade = null;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'city_id',
        'province',
        'province_id',
        'district',
        'district_id',
        'village',
        'village_id',
        'postal_code',
        'notes',
        'address_token',
        'address_verification_url',
        'address_verified_at',
    ];

    protected $casts = [
        'address_verified_at' => 'datetime',
    ];

    protected $appends = [
        'is_address_verified',
    ];

    public function getIsAddressVerifiedAttribute()
    {
        if (is_null($this->address_verified_at)) {
            return false;
        }

        return $this->address_verified_at->diffInDays(now()) <= 7;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($customer) {
            if (empty($customer->address_token)) {
                $customer->address_token = bin2hex(random_bytes(16));
                $customer->address_verification_url = config('app.url') . "/verifikasi-alamat/" . $customer->address_token;
            }
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('phone')) {
                // Simpan nomor lama sebelum record database di-update
                $customer->old_phone_for_cascade = $customer->getOriginal('phone');
            }
        });

        static::updated(function ($customer) {
            if (isset($customer->old_phone_for_cascade) && $customer->old_phone_for_cascade !== $customer->phone) {
                // Update semua SPK yang masih menggunakan nomor lama menjadi nomor baru
                \App\Models\WorkOrder::where('customer_phone', $customer->old_phone_for_cascade)
                    ->update(['customer_phone' => $customer->phone]);
                
                unset($customer->old_phone_for_cascade);
            }
        });
    }

    /**
     * Normalize phone number before saving
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = \App\Helpers\PhoneHelper::normalize($value);
    }

    /**
     * Get all photos for this customer
     */
    public function photos()
    {
        return $this->hasMany(CustomerPhoto::class);
    }

    /**
     * Get all work orders for this customer (matched by phone)
     */
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'customer_phone', 'phone');
    }
}
