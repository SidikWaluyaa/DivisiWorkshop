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
        'village',
        'postal_code',
        'notes',
    ];

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
