<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\StorageRack;

class ManualStorageItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'spk_number',
        'payment_status',
        'total_price',
        'paid_amount',
        'item_name',
        'rack_code',
        'quantity',
        'image_path',
        'description',
        'status',
        'in_date',
        'out_date',
        'stored_by',
        'retrieved_by',
    ];

    protected $casts = [
        'in_date' => 'datetime',
        'out_date' => 'datetime',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function storer()
    {
        return $this->belongsTo(User::class, 'stored_by');
    }

    public function retriever()
    {
        return $this->belongsTo(User::class, 'retrieved_by');
    }
    
    // Optional: Loose relationship to Rack if it exists
    public function rack()
    {
        return $this->belongsTo(StorageRack::class, 'rack_code', 'rack_code');
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }
}
