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

            // Proteksi Duplikasi Cepat (Debounce): Jika record terakhir SPK ini memiliki tipe stage yang sama
            // dan baru saja dibuat dalam rentang 2 menit terakhir, kembalikan record tersebut (mencegah trigger ganda).
            if ($latest && $latest->stage_type === strtoupper($stageType) && $latest->created_at?->diffInMinutes(now()) < 2) {
                return $latest;
            }

            // Determine target round:
            // Sesuai SOP:
            // 1. SPK REGULER & RND HANYA memiliki 1 siklus konfirmasi (After 1 / Round 1).
            //    Jika SPK ini sudah memiliki After 1, DILARANG keras menerbitkan After 2 (kembalikan record yang ada).
            // 2. Hanya GARANSI, REVISI, dan OTO yang berhak masuk ke siklus lanjutan (After 2, After 3, dst).
            $isMultiRoundStage = in_array(strtoupper((string)$stageType), ['GARANSI', 'REVISI', 'OTO']);

            if (!$isMultiRoundStage && is_null($forcedRound)) {
                if ($latest) {
                    return $latest;
                }
                $targetRound = 1;
            } else {
                if (!is_null($forcedRound)) {
                    $existingForced = CxAfterConfirmation::where('work_order_id', $order->id)
                        ->where('round', $forcedRound)
                        ->first();

                    if ($existingForced) {
                        // Jika ronde tersebut sudah terisi (misal Round 2 adalah REVISI), otomatis naik ke ronde berikutnya!
                        $targetRound = max((int)($latest?->round ?? 1) + 1, (int)$forcedRound + 1);
                    } else {
                        $targetRound = $forcedRound;
                    }
                } elseif ($latest) {
                    $targetRound = max((int)$latest->round + 1, 2);
                } else {
                    $targetRound = 2;
                }
            }

            // Batasi hanya jika sudah melampaui batas wajar (misal round > 10)
            if ($latest && (int)$latest->round >= 10) {
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
