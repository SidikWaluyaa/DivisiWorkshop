<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSolution extends Model
{
    protected $fillable = [
        'category',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
