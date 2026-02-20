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
    ];

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
