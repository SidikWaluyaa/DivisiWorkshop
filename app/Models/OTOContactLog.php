<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OTOContactLog extends Model
{
    protected $table = 'oto_contact_logs';

    protected $fillable = [
        'oto_id',
        'contacted_by',
        'contact_method',
        'notes',
        'customer_response',
    ];

    /**
     * Get the OTO that owns this log
     */
    public function oto(): BelongsTo
    {
        return $this->belongsTo(OTO::class, 'oto_id');
    }

    /**
     * Get the user who contacted the customer
     */
    public function contactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }
}
