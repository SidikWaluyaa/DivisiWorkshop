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
            $spk = (string)$order->spk_number;
            $isGr = str_contains($spk, '-GR') || str_contains($spk, 'GR-');
            $isRnd = str_starts_with($spk, 'RD-') || in_array(strtoupper(trim((string)$order->priority)), ['R&D', 'RND']);
            $isOto = !empty($order->has_active_oto);

            // Determine stage_type
            $stageType = $forcedStage;
            if (!$stageType) {
                if ($isGr) {
                    $stageType = 'GARANSI';
                } elseif ($isRnd) {
                    $stageType = 'RND';
                } elseif (!empty($order->is_revising)) {
                    $stageType = 'REVISI';
                } elseif ($isOto) {
                    $stageType = 'OTO';
                } else {
                    $stageType = 'REGULER';
                }
            }

            // Find latest after record for this work order
            $latest = CxAfterConfirmation::where('work_order_id', $order->id)
                ->orderBy('round', 'desc')
                ->first();

            // Determine target round:
            // Khusus GARANSI, default-nya adalah After 2 (round = 2) sesuai standar follow-up rework garansi
            if (!is_null($forcedRound)) {
                $targetRound = $forcedRound;
            } elseif ($stageType === 'GARANSI') {
                $targetRound = 2;
            } elseif ($latest) {
                $targetRound = (int)$latest->round + 1;
            } else {
                $targetRound = 1;
            }

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
