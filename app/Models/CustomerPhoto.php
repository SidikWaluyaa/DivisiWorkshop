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
    protected $appends = ['photo_url'];

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

    /**
     * Get the full URL for the photo
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . $this->file_path);
    }
}
