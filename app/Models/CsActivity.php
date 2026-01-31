<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsActivity extends Model
{
    protected $fillable = [
        'cs_lead_id',
        'user_id',
        'type',
        'channel',
        'content',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Type Constants
    const TYPE_CHAT = 'CHAT';
    const TYPE_CALL = 'CALL';
    const TYPE_EMAIL = 'EMAIL';
    const TYPE_MEETING = 'MEETING';
    const TYPE_NOTE = 'NOTE';
    const TYPE_STATUS_CHANGE = 'STATUS_CHANGE';
    const TYPE_QUOTATION_SENT = 'QUOTATION_SENT';
    const TYPE_QUOTATION_ACCEPTED = 'QUOTATION_ACCEPTED';
    const TYPE_QUOTATION_REJECTED = 'QUOTATION_REJECTED';

    /**
     * Relationships
     */
    public function lead()
    {
        return $this->belongsTo(CsLead::class, 'cs_lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Helper Methods
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            self::TYPE_CHAT => '💬',
            self::TYPE_CALL => '📞',
            self::TYPE_EMAIL => '📧',
            self::TYPE_MEETING => '🤝',
            self::TYPE_NOTE => '📝',
            self::TYPE_STATUS_CHANGE => '🔄',
            self::TYPE_QUOTATION_SENT => '📤',
            self::TYPE_QUOTATION_ACCEPTED => '✅',
            self::TYPE_QUOTATION_REJECTED => '❌',
            default => '📌',
        };
    }

    public function getFormattedContentAttribute()
    {
        return nl2br(e($this->content));
    }
}
