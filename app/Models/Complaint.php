<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $work_order_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $category
 * @property string $description
 * @property array $photos
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Complaint extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'work_order_id',
        'customer_name',
        'customer_phone',
        'category',
        'description',
        'photos',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'photos' => 'array',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}
