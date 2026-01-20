<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'notes',
    ];

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
