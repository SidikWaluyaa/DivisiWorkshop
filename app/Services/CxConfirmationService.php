<?php

namespace App\Services;

use App\Models\WorkOrder;
use App\Models\CxAfterConfirmation;
use Illuminate\Support\Facades\Log;

class CxConfirmationService
{
    /**
     * Create or trigger a confirmation record for an order.
     * Supports multi-cycle confirmation (After 1 and After 2).
     *
     * @param WorkOrder $order
     * @param string|null $forcedStage Optional forced stage: 'GARANSI', 'REVISI', 'OTO', 'RND', 'REGULER'
     * @param int|null $forcedRound Optional forced round
     * @return CxAfterConfirmation|null
     */
    public function createFromOrder(WorkOrder $order, ?string $forcedStage = null, ?int $forcedRound = null): ?CxAfterConfirmation
    {
        try {
            // Find latest after record for this work order
            $latest = CxAfterConfirmation::where('work_order_id', $order->id)
                ->orderBy('round', 'desc')
                ->first();

            // Determine target round
            $targetRound = $forcedRound ?? ($latest ? ((int)$latest->round + 1) : 1);

            // Cap at round 2 unless specifically forced
            if (is_null($forcedRound) && $latest && (int)$latest->round >= 2) {
                return null;
            }

            // Prevent creating duplicate records for the exact same round
            $duplicateCheck = CxAfterConfirmation::where('work_order_id', $order->id)
                ->where('round', $targetRound)
                ->first();

            if ($duplicateCheck) {
                return $duplicateCheck;
            }

            // Determine stage_type
            $stageType = $forcedStage;
            if (!$stageType) {
                if (!empty($order->is_warranty) || str_contains((string)$order->spk_number, '-GR') || str_contains((string)$order->category_spk, 'Garansi')) {
                    $stageType = 'GARANSI';
                } elseif (in_array(strtoupper(trim((string)$order->priority)), ['R&D', 'RND'])) {
                    $stageType = 'RND';
                } elseif (!empty($order->is_revising) || $targetRound >= 2) {
                    $stageType = 'REVISI';
                } elseif (!empty($order->has_active_oto)) {
                    $stageType = 'OTO';
                } else {
                    $stageType = 'REGULER';
                }
            }

            $record = CxAfterConfirmation::create([
                'work_order_id' => $order->id,
                'round'         => $targetRound,
                'stage_type'    => strtoupper($stageType),
                'entered_at'    => now(),
            ]);

            Log::info("Created Konfirmasi After record for SPK: {$order->spk_number} (Round: {$targetRound}, Stage: {$stageType})");

            return $record;
        } catch (\Exception $e) {
            Log::error("Failed to create Konfirmasi After record: " . $e->getMessage());
            return null;
        }
    }
}
