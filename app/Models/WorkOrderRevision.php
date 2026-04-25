<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderRevision extends Model
{
    protected $fillable = [
        'work_order_id',
        'description',
        'photo_path',
        'photo_paths',
        'status',
        'created_by',
        'resolved_by',
        'finished_at',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
        'photo_paths' => 'array',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function getPhotoUrlsAttribute(): array
    {
        $urls = [];
        if ($this->photo_path) {
            $urls[] = asset('storage/' . $this->photo_path);
        }
        
        if ($this->photo_paths && is_array($this->photo_paths)) {
            foreach ($this->photo_paths as $path) {
                $urls[] = asset('storage/' . $path);
            }
        }
        
        return $urls;
    }
}
