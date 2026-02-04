<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'required_services',
    ];

    protected $casts = [
        'required_services' => 'array',
    ];

    /**
     * Relationships
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Check if selected services meet bundle requirements
     */
    public function isApplicable(array $selectedServiceIds): bool
    {
        $requiredServices = $this->required_services ?? [];
        
        // Check if all required services are in the selected services
        foreach ($requiredServices as $requiredId) {
            if (!in_array($requiredId, $selectedServiceIds)) {
                return false;
            }
        }

        return true;
    }
}
