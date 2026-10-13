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
            // Sesuai SOP: GARANSI, REVISI, dan OTO minimal adalah After Ke-2 (round = 2).
            // Jika sudah ada After sebelumnya (misal OTO ke-2, Revisi ke-2), otomatis naik ke After 3, 4, dst.
            if (!is_null($forcedRound)) {
                $targetRound = $forcedRound;
            } elseif ($latest) {
                $targetRound = (int)$latest->round + 1;
            } elseif (in_array(strtoupper((string)$stageType), ['GARANSI', 'REVISI', 'OTO'])) {
                $targetRound = 2;
            } else {
                $targetRound = 1;
            }

            // Batasi hanya jika sudah melampaui batas wajar (misal round > 10)
            if (is_null($forcedRound) && $latest && (int)$latest->round >= 10) {
                return null;
            }

            // Untuk REVISI dan OTO di Round 2, pastikan Round 1 (After 1) sudah ada
            if ($targetRound >= 2 && !$isGr) {
                $hasRound1 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->exists();
                if (!$hasRound1) {
                    CxAfterConfirmation::create([
                        'work_order_id' => $order->id,
                        'round'         => 1,
                        'stage_type'    => $isRnd ? 'RND' : 'REGULER',
                        'entered_at'    => $order->finished_date ?? $order->entry_date ?? $order->created_at ?? now(),
                    ]);
                }
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
