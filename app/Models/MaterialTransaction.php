<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialTransaction extends Model
{
    protected $fillable = [
        'material_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    /**
     * Relationships
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getReferenceSpkAttribute()
    {
        if ($this->reference_type === 'WorkOrder') {
            $wo = WorkOrder::find($this->reference_id);
            return $wo ? $wo->spk_number : null;
        }
        return null;
    }

    public function getReferenceReqAttribute()
    {
        if ($this->reference_type === 'MaterialRequest') {
            $req = MaterialRequest::find($this->reference_id);
            return $req ? $req->request_number : null;
        }
        return null;
    }

    public function getReferenceLabelAttribute()
    {
        if ($this->reference_type === 'WorkOrder') {
            return $this->reference_spk;
        }
        if ($this->reference_type === 'MaterialRequest') {
            return $this->reference_req;
        }
        return null;
    }

    public function reference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }
        
        $modelClass = "App\\Models\\" . $this->reference_type;
        if (class_exists($modelClass)) {
            return $modelClass::find($this->reference_id);
        }
        
        return null;
    }
}
