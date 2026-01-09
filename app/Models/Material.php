<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name',
        'category',
        'stock',
        'unit',
        'price',
        'min_stock',
        'status',
        'pic_user_id',
    ];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }
}
