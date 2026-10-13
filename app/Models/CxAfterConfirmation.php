<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CxAfterConfirmation extends Model
{
    protected $fillable = [
        'work_order_id',
        'round',
        'stage_type',
        'entered_at',
        'contacted_at',
        'pic_id',
        'response',
        'notes'
    ];

    protected $casts = [
        'round' => 'integer',
        'entered_at' => 'datetime',
        'contacted_at' => 'datetime',
    ];

    /**
     * Get style tokens for SPK badges based on stage_type
     */
    public function getStageBadgeAttribute(): array
    {
        return match(strtoupper((string)($this->stage_type ?? 'REGULER'))) {
            'GARANSI' => [
                'bg' => 'bg-amber-50',
                'border' => 'border-amber-300',
                'text' => 'text-amber-700',
                'ring' => 'ring-amber-300/60',
                'dot' => 'bg-amber-500',
                'label' => 'GARANSI',
                'spk_bg' => 'bg-amber-500/10 text-amber-700 border-amber-300 ring-1 ring-amber-400/30',
            ],
            'REVISI' => [
                'bg' => 'bg-rose-50',
                'border' => 'border-rose-300',
                'text' => 'text-rose-700',
                'ring' => 'ring-rose-300/60',
                'dot' => 'bg-rose-500',
                'label' => 'REVISI',
                'spk_bg' => 'bg-rose-500/10 text-rose-700 border-rose-300 ring-1 ring-rose-400/30',
            ],
            'OTO' => [
                'bg' => 'bg-orange-50',
                'border' => 'border-orange-300',
                'text' => 'text-orange-700',
                'ring' => 'ring-orange-300/60',
                'dot' => 'bg-orange-500',
                'label' => 'OTO',
                'spk_bg' => 'bg-orange-500/10 text-orange-700 border-orange-300 ring-1 ring-orange-400/30',
            ],
            'RND' => [
                'bg' => 'bg-blue-50',
                'border' => 'border-blue-300',
                'text' => 'text-blue-700',
                'ring' => 'ring-blue-300/60',
                'dot' => 'bg-blue-500',
                'label' => 'R&D',
                'spk_bg' => 'bg-blue-500/10 text-blue-700 border-blue-300 ring-1 ring-blue-400/30',
            ],
            default => [
                'bg' => 'bg-emerald-50',
                'border' => 'border-[#22AF85]/30',
                'text' => 'text-[#22AF85]',
                'ring' => 'ring-[#22AF85]/20',
                'dot' => 'bg-[#22AF85]',
                'label' => 'REGULER',
                'spk_bg' => 'bg-[#22AF85]/10 text-[#22AF85] border-[#22AF85]/30 ring-1 ring-[#22AF85]/20',
            ],
        };
    }

    /**
     * Get style tokens for round badge (After 1, After 2, After 3+)
     */
    public function getRoundBadgeAttribute(): array
    {
        $round = (int)($this->round ?? 1);

        if ($round >= 3) {
            return [
                'label' => 'After ' . $round,
                'badge_class' => 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/20 font-black',
                'indicator' => (string)$round,
            ];
        }

        if ($round === 2) {
            return [
                'label' => 'After 2',
                'badge_class' => 'bg-purple-600 text-white shadow-sm shadow-purple-500/20 font-black',
                'indicator' => '2',
            ];
        }

        return [
            'label' => 'After 1',
            'badge_class' => 'bg-slate-700 text-white shadow-sm font-black',
            'indicator' => '1',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }
}
