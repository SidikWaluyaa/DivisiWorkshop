<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
use App\Models\WorkOrderRevision;
use App\Models\OTO;
use App\Models\CxAfterConfirmation;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;

class SyncCxAfterConfirmationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cx:sync-after-confirmation {--force : Force sync all historical records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync historical Work Orders (SPK Garansi, Revisi After 2, OTO, R&D) into cx_after_confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Memulai Sinkronisasi Konfirmasi After Service...");

        $syncedGaransiRound2 = 0;
        $syncedRevisiRound2 = 0;
        $syncedOtoRound2 = 0;
        $syncedRnd = 0;
        $updatedRecords = 0;

        // 1. SINKRONISASI GARANSI (work_order_warranties status = FINISHED -> AFTER 2 / ROUND = 2)
        $this->info("⏳ 1. Memeriksa tabel work_order_warranties (status = FINISHED) & SPK -GR...");
        
        $finishedWarranties = WorkOrderWarranty::where('status', 'FINISHED')
            ->whereNotNull('garansi_spk_number')
            ->get();

        foreach ($finishedWarranties as $w) {
            $reworkWo = WorkOrder::where('spk_number', $w->garansi_spk_number)->first();
            if ($reworkWo) {
                $existing = CxAfterConfirmation::where('work_order_id', $reworkWo->id)->where('round', 2)->first();
                if (!$existing) {
                    $wrongRound1 = CxAfterConfirmation::where('work_order_id', $reworkWo->id)->where('round', 1)->first();
                    if ($wrongRound1) {
                        $wrongRound1->update([
                            'round'      => 2,
                            'stage_type' => 'GARANSI',
                        ]);
                        $updatedRecords++;
                    } else {
                        CxAfterConfirmation::create([
                            'work_order_id' => $reworkWo->id,
                            'round'         => 2,
                            'stage_type'    => 'GARANSI',
                            'entered_at'    => $reworkWo->finished_date ?? $w->finished_at ?? $reworkWo->created_at ?? now(),
                        ]);
                        $syncedGaransiRound2++;
                    }
                } elseif ($existing->stage_type !== 'GARANSI') {
                    $existing->update(['stage_type' => 'GARANSI']);
                    $updatedRecords++;
                }
            }
        }

        // Pastikan juga semua work orders dengan nomor SPK -GR yang sudah SELESAI masuk ke Round 2 GARANSI
        $grOrders = WorkOrder::where(function ($q) {
            $q->where('spk_number', 'like', '%-GR%')
              ->orWhere('spk_number', 'like', 'GR-%')
              ->orWhere('spk_number', 'like', '%-GR');
        })
        ->whereIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR, WorkOrderStatus::HISTORY])
        ->get();

        foreach ($grOrders as $gr) {
            $existing = CxAfterConfirmation::where('work_order_id', $gr->id)->where('round', 2)->first();
            if (!$existing) {
                $wrongRound1 = CxAfterConfirmation::where('work_order_id', $gr->id)->where('round', 1)->first();
                if ($wrongRound1) {
                    $wrongRound1->update([
                        'round'      => 2,
                        'stage_type' => 'GARANSI',
                    ]);
                    $updatedRecords++;
                } else {
                    CxAfterConfirmation::create([
                        'work_order_id' => $gr->id,
                        'round'         => 2,
                        'stage_type'    => 'GARANSI',
                        'entered_at'    => $gr->finished_date ?? $gr->entry_date ?? $gr->created_at ?? now(),
                    ]);
                    $syncedGaransiRound2++;
                }
            }
        }

        // 2. SINKRONISASI REVISI (HANYA DARI SELESAI / FINISH, BUKAN QC / PRODUCTION)
        $this->info("⏳ 2. Memeriksa tabel work_order_revisions (status = FINISHED & origin = SELESAI)...");
        
        $revisionsByOrder = WorkOrderRevision::where('status', 'FINISHED')
            ->where(function ($q) {
                $q->whereNull('origin_status')
                  ->orWhere('origin_status', 'SELESAI')
                  ->orWhere('origin_status', '');
            })
            ->orderBy('finished_at', 'asc')
            ->with('workOrder')
            ->get()
            ->groupBy('work_order_id');

        foreach ($revisionsByOrder as $woId => $revisions) {
            $order = $revisions->first()?->workOrder;
            if (!$order) continue;

            $isOrderRnd = str_starts_with((string)$order->spk_number, 'RD-') || in_array(strtoupper(trim((string)$order->priority)), ['R&D', 'RND']);

            // Pastikan After 1 ada (sebagai REGULER atau RND)
            $after1 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$after1) {
                $after1 = CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => $isOrderRnd ? 'RND' : 'REGULER',
                    'entered_at'    => $order->finished_date ?? $order->created_at ?? now(),
                ]);
            }

            // Daftarkan setiap revisi dari Finish yang selesai secara sekuensial (Round 2, Round 3, dst.)
            foreach ($revisions as $idx => $rev) {
                $revRound = 2 + $idx;
                $existing = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', $revRound)->first();
                if (!$existing) {
                    CxAfterConfirmation::create([
                        'work_order_id' => $order->id,
                        'round'         => $revRound,
                        'stage_type'    => 'REVISI',
                        'entered_at'    => $rev->finished_at ?? $rev->updated_at ?? now(),
                    ]);
                    $syncedRevisiRound2++;
                } else {
                    if ($existing->stage_type !== 'REVISI') {
                        $existing->update(['stage_type' => 'REVISI']);
                        $updatedRecords++;
                    }
                }
            }
        }

        // 3. SINKRONISASI OTO (otos status = COMPLETED -> AFTER 2, AFTER 3, dst.)
        $this->info("⏳ 3. Memeriksa tabel otos (status = COMPLETED)...");
        
        $otosByOrder = OTO::where('status', 'COMPLETED')
            ->orderBy('completed_at', 'asc')
            ->with('workOrder')
            ->get()
            ->groupBy('work_order_id');

        foreach ($otosByOrder as $woId => $otos) {
            $order = $otos->first()?->workOrder;
            if (!$order) continue;

            $isOrderRnd = str_starts_with((string)$order->spk_number, 'RD-') || in_array(strtoupper(trim((string)$order->priority)), ['R&D', 'RND']);

            // Pastikan After 1 ada (sebagai REGULER atau RND)
            $after1 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$after1) {
                $after1 = CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => $isOrderRnd ? 'RND' : 'REGULER',
                    'entered_at'    => $order->finished_date ?? $order->entry_date ?? $order->created_at ?? now(),
                ]);
            }

            // Cari ronde tertinggi yang sudah terpakai selain OTO (misal jika ada Revisi sebelumnya)
            $baseRound = 2;
            $maxNonOto = CxAfterConfirmation::where('work_order_id', $order->id)
                ->where('stage_type', '!=', 'OTO')
                ->where('round', '>=', 2)
                ->max('round');
            if ($maxNonOto) {
                $baseRound = $maxNonOto + 1;
            }

            // Daftarkan setiap paket OTO yang selesai secara sekuensial (Round 2, Round 3, dst.)
            foreach ($otos as $idx => $oto) {
                $otoRound = $baseRound + $idx;
                $existing = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', $otoRound)->first();
                if (!$existing) {
                    CxAfterConfirmation::create([
                        'work_order_id' => $order->id,
                        'round'         => $otoRound,
                        'stage_type'    => 'OTO',
                        'entered_at'    => $oto->completed_at ?? $oto->updated_at ?? now(),
                    ]);
                    $syncedOtoRound2++;
                } else {
                    if ($existing->stage_type !== 'OTO') {
                        $existing->update(['stage_type' => 'OTO']);
                        $updatedRecords++;
                    }
                }
            }
        }

        // 4. SINKRONISASI R&D (RD-... / priority = R&D / RND -> AFTER 1 / ROUND = 1)
        $this->info("⏳ 4. Memeriksa SPK R&D (RD-... / priority R&D seperti reguler di round 1)...");
        $rndOrders = WorkOrder::where(function ($q) {
            $q->whereIn('priority', ['R&D', 'RND'])
              ->orWhere('spk_number', 'like', 'RD-%');
        })
        ->whereIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR, WorkOrderStatus::HISTORY])
        ->get();

        foreach ($rndOrders as $order) {
            $existing = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$existing) {
                CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => 'RND',
                    'entered_at'    => $order->finished_date ?? $order->entry_date ?? $order->created_at ?? now(),
                ]);
                $syncedRnd++;
            } elseif ($existing->stage_type !== 'RND') {
                $existing->update(['stage_type' => 'RND']);
                $updatedRecords++;
            }
        }

        // 5. RAPATKAN, BERSIHKAN REVISI INTERNAL (QC/PRODUCTION), DAN URUTKAN NOMOR RONDE
        $this->info("⏳ 5. Membersihkan revisi internal (QC/Production) & menata ulang nomor ronde sekuensial...");
        
        // Hapus record REVISI yang bukan berasal dari Selesai/Finish (revisi internal QC/Production)
        $allRevisis = CxAfterConfirmation::where('stage_type', 'REVISI')->get();
        foreach ($allRevisis as $revRecord) {
            $hasFinishRev = WorkOrderRevision::where('work_order_id', $revRecord->work_order_id)
                ->where('status', 'FINISHED')
                ->where(function ($q) {
                    $q->whereNull('origin_status')
                      ->orWhere('origin_status', 'SELESAI')
                      ->orWhere('origin_status', '');
                })
                ->exists();

            if (!$hasFinishRev) {
                $revRecord->delete();
                $updatedRecords++;
            }
        }

        $allOrdersGroup = CxAfterConfirmation::with('workOrder')
            ->orderBy('entered_at', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('work_order_id');

        foreach ($allOrdersGroup as $woId => $records) {
            $wo = $records->first()?->workOrder;
            if (!$wo) continue;

            $isGr = str_contains((string)$wo->spk_number, '-GR') || str_contains((string)$wo->spk_number, 'GR-');
            $isRnd = str_starts_with((string)$wo->spk_number, 'RD-') || in_array(strtoupper(trim((string)$wo->priority)), ['R&D', 'RND']);

            // Jika ini SPK Garansi Rework (-GR), mulai dari round 2
            $startRound = $isGr ? 2 : 1;

            foreach ($records as $seqIdx => $record) {
                $targetRound = $startRound + $seqIdx;

                // Tentukan stage yang benar jika round 1
                $targetStage = $record->stage_type;
                if ($targetRound === 1) {
                    $targetStage = $isRnd ? 'RND' : 'REGULER';
                } elseif ($isGr) {
                    $targetStage = 'GARANSI';
                }

                if ((int)$record->round !== $targetRound || $record->stage_type !== $targetStage) {
                    $record->update([
                        'round'      => $targetRound,
                        'stage_type' => $targetStage,
                    ]);
                    $updatedRecords++;
                }
            }
        }

        $this->newLine();
        $this->info("✅ SINKRONISASI SELESAI!");
        $this->table(
            ['Kategori Sinkronisasi', 'Jumlah Diproses'],
            [
                ['SPK Garansi (-GR) Masuk (Round 2 / After 2)', $syncedGaransiRound2],
                ['After Ke-2 Pasca Revisi Selesai (Round 2)', $syncedRevisiRound2],
                ['After Ke-2 Pasca OTO Completed (Round 2)', $syncedOtoRound2],
                ['SPK R&D Masuk (Round 1 / After 1)', $syncedRnd],
                ['Record Eksisting Dikoreksi / Divalidasi', $updatedRecords],
                ['Total Record cx_after_confirmations Sekarang', CxAfterConfirmation::count()]
            ]
        );

        return Command::SUCCESS;
    }
}
