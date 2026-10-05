<?php

namespace App\Traits;

use App\Models\WorkOrderLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Enums\WorkOrderStatus;

trait HasStationTracking
{
    /**
     * Handle generic station update (start/finish)
     * 
     * @param \App\Models\WorkOrder $order
     * @param string $type e.g., 'washing', 'sol', 'prod_sol', 'qc_jahit'
     * @param string $action 'start' or 'finish'
     * @param int $techId ID of the logged in user performing the action
     * @param int|null $assigneeId ID of the technician being assigned (for start action)
     * @param string $logStep The workflow step enum string/value
     * @param string|null $finishedAt Optional manual finish date (Y-m-d)
     */
    protected function handleStationUpdate($order, $type, $action, $techId, $assigneeId, $logStep, $finishedAt = null)
    {
        $now = Carbon::now();
        // Determine column prefix. 
        // If type already has 'prod_' or 'qc_', use it as is? 
        // No, current logic prepends 'prep_'. 
        // Let's standardize: we pass the full prefix or just the dynamic part?
        // Let's make the $type argument fully dynamic mapping to column name segment.
        // e.g., preparation uses 'prep_washing', 'prep_sol'.
        // Production uses 'prod_sol', 'prod_cleaning'.
        // So we should probably pass the full prefix part as $type.
        
        // However, PreparationController calls it with 'washing', 'sol' and expects 'prep_' prefix.
        // To support legacy PreparationController without major refactor, we can check.
        
        $columnPrefix = $type; 
        
        // Auto-prefix for preparation legacy calls if needed, OR just refactor PreparationController later.
        // For now, let's assume the caller passes the EXACT column prefix base.
        // e.g. 'prep_washing', 'prod_sol', 'qc_final'.
        
        if ($action === 'start') {
            $finalTechId = $assigneeId ?: $order->{"{$columnPrefix}_by"};
            if (!$finalTechId) {
                throw new \Exception('Pilih teknisi terlebih dahulu sebelum memulai stasiun.');
            }
            $order->{"{$columnPrefix}_by"} = (int)$finalTechId;
            $order->{"{$columnPrefix}_started_at"} = $now;
        
            $logDescription = "Memulai proses " . $this->formatStationName($type);
        } else {
            // Use manual date if provided, otherwise Use NOW
            $completionTime = $finishedAt ? Carbon::parse($finishedAt)->setTimeFrom($now) : $now;
            
            $order->{"{$columnPrefix}_completed_at"} = $completionTime;
            // Do not overwrite assigned technician if it exists, unless explicitly provided
            if ($assigneeId) {
                $order->{"{$columnPrefix}_by"} = (int)$assigneeId;
            } elseif (!$order->{"{$columnPrefix}_by"}) {
                throw new \Exception("SPK {$order->spk_number} belum ditugaskan ke teknisi. Silakan pilih teknisi terlebih dahulu sebelum menyelesaikan.");
            }
            
            $dateNote = $finishedAt ? " (Manual: $finishedAt)" : "";
            $logDescription = "Menyelesaikan proses " . $this->formatStationName($type) . $dateNote;

            // Jika stasiun saat ini dalam keadaan dijeda, tutup jeda otomatis terlebih dahulu
            $pauseInfo = $this->getStationPauseInfo($order, $type);
            if ($pauseInfo['is_paused']) {
                $this->resumeStationTracking($order, $type, $techId, $logStep);
            }
        }

        // Determine who should be logged as the actor
        $logUserId = $techId;

        if ($action === 'start') {
            $logUserId = $order->{"{$columnPrefix}_by"} ?: $techId;
        } elseif ($action === 'finish') {
            // If finishing, try to attribute to the assigned technician if they exist
            // This is useful when Admin finishes a task on behalf of a technician
            if ($order->{"{$columnPrefix}_by"}) {
                $logUserId = $order->{"{$columnPrefix}_by"};
            }
        }

        // Save log
        WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => $logUserId,
            'action' => $type . '_' . $action, // e.g. prep_washing_start
            'description' => $logDescription,
            'step' => $logStep
        ]);
    }

    /**
     * Pause a station progress with preset reason and optional note.
     */
    public function pauseStationTracking($order, string $station, string $reason, int $userId, string $step = 'production', ?string $customNote = null)
    {
        $pauseInfo = $this->getStationPauseInfo($order, $station);
        if ($pauseInfo['is_paused']) {
            throw new \Exception("Stasiun " . $this->formatStationName($station) . " sudah dalam status dijeda.");
        }

        $notePart = $customNote ? " - " . trim($customNote) : "";
        $description = "[JEDA: {$reason}]{$notePart}";

        WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => $userId,
            'action' => "{$station}_pause",
            'description' => $description,
            'step' => $step,
        ]);

        if ($order->relationLoaded('logs')) {
            $order->load('logs.user');
        }
    }

    /**
     * Resume a paused station progress.
     */
    public function resumeStationTracking($order, string $station, int $userId, string $step = 'production')
    {
        $pauseInfo = $this->getStationPauseInfo($order, $station);
        if (!$pauseInfo['is_paused']) {
            return;
        }

        $pausedSeconds = $pauseInfo['current_pause_seconds'];
        $pausedMinutes = max(1, (int) round($pausedSeconds / 60));
        $description = "[LANJUT] Melanjutkan pengerjaan setelah jeda {$pausedMinutes} menit";

        WorkOrderLog::create([
            'work_order_id' => $order->id,
            'user_id' => $userId,
            'action' => "{$station}_resume",
            'description' => $description,
            'step' => $step,
        ]);

        if ($order->relationLoaded('logs')) {
            $order->load('logs.user');
        }
    }

    /**
     * Get pause metadata and net active duration for a station.
     */
    public function getStationPauseInfo($order, string $station): array
    {
        $logs = $order->relationLoaded('logs')
            ? $order->logs
            : $order->logs()->whereIn('action', ["{$station}_pause", "{$station}_resume"])->orderBy('id', 'asc')->get();

        $stationLogs = $logs->filter(fn($l) => in_array($l->action, ["{$station}_pause", "{$station}_resume"], true))->sortBy('id')->values();

        $totalPausedSeconds = 0;
        $isPaused = false;
        $currentPauseSeconds = 0;
        $lastReason = null;
        $pausedAt = null;

        $lastPauseLog = null;
        foreach ($stationLogs as $log) {
            if ($log->action === "{$station}_pause") {
                $lastPauseLog = $log;
                $isPaused = true;
                $pausedAt = $log->created_at;
                if (preg_match('/\[JEDA:\s*([^\]]+)\]/', $log->description, $m)) {
                    $lastReason = trim($m[1]);
                } else {
                    $lastReason = 'Dijeda';
                }
            } elseif ($log->action === "{$station}_resume") {
                if ($lastPauseLog) {
                    $diff = max(0, Carbon::parse($log->created_at)->getTimestamp() - Carbon::parse($lastPauseLog->created_at)->getTimestamp());
                    $totalPausedSeconds += $diff;
                    $lastPauseLog = null;
                    $isPaused = false;
                    $pausedAt = null;
                }
            }
        }

        if ($isPaused && $lastPauseLog) {
            $currentPauseSeconds = max(0, Carbon::now()->getTimestamp() - Carbon::parse($lastPauseLog->created_at)->getTimestamp());
            $totalPausedSeconds += $currentPauseSeconds;
        }

        // Net active working time
        $startedAt = $order->{"{$station}_started_at"} ? Carbon::parse($order->{"{$station}_started_at"}) : null;
        $completedAt = $order->{"{$station}_completed_at"} ? Carbon::parse($order->{"{$station}_completed_at"}) : null;

        $netElapsedSeconds = 0;
        if ($startedAt) {
            $endPoint = $completedAt ?: ($isPaused && $pausedAt ? Carbon::parse($pausedAt) : Carbon::now());
            $grossSeconds = max(0, $endPoint->getTimestamp() - $startedAt->getTimestamp());
            $resolvedPaused = $isPaused ? ($totalPausedSeconds - $currentPauseSeconds) : $totalPausedSeconds;
            $netElapsedSeconds = max(0, $grossSeconds - $resolvedPaused);
        }

        return [
            'is_paused' => $isPaused,
            'reason' => $lastReason,
            'paused_at' => $pausedAt ? Carbon::parse($pausedAt) : null,
            'total_paused_seconds' => (int) $totalPausedSeconds,
            'current_pause_seconds' => (int) $currentPauseSeconds,
            'net_elapsed_seconds' => (int) $netElapsedSeconds,
        ];
    }

    protected function formatStationName($type)
    {
        return ucwords(str_replace('_', ' ', $type));
    }
}

