<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
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

        static::saved(function ($customer) {
            if ($customer->isDirty('phone')) {
                $oldPhone = $customer->getOriginal('phone');
                $newPhone = $customer->phone;

                if ($oldPhone && $newPhone) {
                    \App\Models\WorkOrder::where('customer_phone', $oldPhone)
                        ->update(['customer_phone' => $newPhone]);
                }
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
