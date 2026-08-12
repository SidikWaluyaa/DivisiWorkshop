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
        'origin_status',
        'qc_stage',
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
        if (!$this->photo_path) return null;
        
        $path = $this->photo_path;
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8); // remove 'storage/'
        }
        return asset('storage/' . $path);
    }

    public function getPhotoUrlsAttribute(): array
    {
        $urls = [];
        $paths = [];
        
        if ($this->photo_path) {
            $paths[] = $this->photo_path;
        }
        
        if ($this->photo_paths && is_array($this->photo_paths)) {
            foreach ($this->photo_paths as $path) {
                $paths[] = $path;
            }
        }
        
        $paths = array_unique($paths);
        
        foreach ($paths as $path) {
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8); // remove 'storage/'
            }
            $urls[] = asset('storage/' . $path);
        }
        
        return $urls;
    }
}
