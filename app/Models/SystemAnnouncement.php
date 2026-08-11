<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'title',
        'category',
        'summary',
        'description',
        'target_roles',
        'is_active',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(UserAnnouncementRead::class, 'announcement_id');
    }

    public function isReadBy(User $user): bool
    {
        return $this->reads()->where('user_id', $user->id)->exists();
    }
}
