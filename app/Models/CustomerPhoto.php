<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPhoto extends Model
{
    protected $fillable = [
        'customer_id',
        'file_path',
        'caption',
        'type',
        'uploaded_by',
    ];

    /**
     * Get the customer that owns this photo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who uploaded this photo
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
