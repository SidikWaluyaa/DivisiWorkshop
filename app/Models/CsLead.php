<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CsLead extends Model
{
    use HasFactory, SoftDeletes;

    // Status Constants (New Pipeline System)
    const STATUS_GREETING = 'GREETING';
    const STATUS_KONSULTASI = 'KONSULTASI';
    const STATUS_CLOSING = 'CLOSING';
    const STATUS_CONVERTED = 'CONVERTED';
    const STATUS_LOST = 'LOST';

    // Priority Constants
    const PRIORITY_HOT = 'HOT';
    const PRIORITY_WARM = 'WARM';
    const PRIORITY_COLD = 'COLD';

    // Source Constants
    const SOURCE_WHATSAPP = 'WhatsApp';
    const SOURCE_INSTAGRAM = 'Instagram';
    const SOURCE_WEBSITE = 'Website';
    const SOURCE_REFERRAL = 'Referral';
    const SOURCE_WALKIN = 'Walk-in';

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_city',
        'customer_province',
        'status',
        'cs_id',
        'last_activity_at',
        'notes',
        // New fields
        'source',
        'source_detail',
        'first_contact_at',
        'first_response_at',
        'response_time_minutes',
        'priority',
        'expected_value',
        'lost_reason',
        'converted_to_work_order_id',
        'next_follow_up_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'first_contact_at' => 'datetime',
        'first_response_at' => 'datetime',
        'expected_value' => 'decimal:2',
        'next_follow_up_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function cs()
    {
        return $this->belongsTo(User::class, 'cs_id');
    }

    public function activities()
    {
        return $this->hasMany(CsActivity::class);
    }

    public function quotations()
    {
        return $this->hasMany(CsQuotation::class);
    }

    public function spk()
    {
        return $this->hasOne(CsSpk::class);
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'converted_to_work_order_id');
    }

    /**
     * Scopes
     */
    public function scopeGreeting($query)
    {
        return $query->where('status', self::STATUS_GREETING);
    }

    public function scopeKonsultasi($query)
    {
        return $query->where('status', self::STATUS_KONSULTASI);
    }

    public function scopeClosing($query)
    {
        return $query->where('status', self::STATUS_CLOSING);
    }

    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_CONVERTED);
    }

    public function scopeLost($query)
    {
        return $query->where('status', self::STATUS_LOST);
    }

    public function scopeHotLeads($query)
    {
        return $query->where('priority', self::PRIORITY_HOT);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Helper Methods
     */
    public function calculateResponseTime()
    {
        if ($this->first_contact_at && $this->first_response_at) {
            $this->response_time_minutes = $this->first_contact_at->diffInMinutes($this->first_response_at);
            $this->save();
        }
    }

    public function getLatestQuotation()
    {
        return $this->quotations()->latest('version')->first();
    }

    public function getAcceptedQuotation()
    {
        return $this->quotations()->where('status', CsQuotation::STATUS_ACCEPTED)->latest()->first();
    }

    public function canMoveToKonsultasi()
    {
        return $this->status === self::STATUS_GREETING;
    }

    public function canMoveToClosing()
    {
        return $this->status === self::STATUS_KONSULTASI && 
               $this->getAcceptedQuotation() !== null;
    }

    public function canGenerateSpk()
    {
        return $this->status === self::STATUS_CLOSING && 
               !$this->spk;
    }

    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            self::PRIORITY_HOT => 'bg-red-100 text-red-700 border-red-200',
            self::PRIORITY_WARM => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            self::PRIORITY_COLD => 'bg-blue-100 text-blue-700 border-blue-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_GREETING => 'bg-green-100 text-green-700',
            self::STATUS_KONSULTASI => 'bg-yellow-100 text-yellow-700',
            self::STATUS_CLOSING => 'bg-blue-100 text-blue-700',
            self::STATUS_CONVERTED => 'bg-purple-100 text-purple-700',
            self::STATUS_LOST => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getResponseTimeFormattedAttribute()
    {
        if (!$this->response_time_minutes) return 'N/A';
        
        if ($this->response_time_minutes < 60) {
            return $this->response_time_minutes . ' menit';
        } else {
            $hours = floor($this->response_time_minutes / 60);
            $minutes = $this->response_time_minutes % 60;
            return $hours . 'j ' . $minutes . 'm';
        }
    }

    public function getDaysInStageAttribute()
    {
        return $this->last_activity_at ? $this->last_activity_at->diffInDays(now()) : 0;
    }

    public function getWaGreetingLinkAttribute()
    {
        $phone = $this->customer_phone;
        // Clean phone number (remove non-digits)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure it starts with 62
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        } elseif (strpos($phone, '8') === 0) {
            $phone = '62' . $phone;
        }

        $message = "Halo " . ($this->customer_name ?? 'Kak') . ", Kami dari Antigravity Workshop. Ada yang bisa kami bantu terkait layanan kami?";
        return "https://wa.me/" . $phone . "?text=" . urlencode($message);
    }
}
