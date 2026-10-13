<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\WorkOrderWarranty;
use App\Models\WorkOrderRevision;
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
        $syncedOto = 0;
        $syncedRnd = 0;
        $updatedStages = 0;

        // 1. SINKRONISASI SPK GARANSI (HANYA KODE -GR / GR-, WAJIB AFTER KE-2 / ROUND = 2)
        $this->info("⏳ 1. Memeriksa SPK Garansi (-GR, round = 2)...");
        
        // Ambil semua work orders garansi yang sudah SELESAI
        $garansiOrders = WorkOrder::where(function ($q) {
            $q->where('spk_number', 'like', '%-GR%')
              ->orWhere('spk_number', 'like', 'GR-%')
              ->orWhere('spk_number', 'like', '%-GR');
        })
        ->whereIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR, WorkOrderStatus::HISTORY])
        ->get();

        foreach ($garansiOrders as $order) {
            $existing = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 2)->first();
            if (!$existing) {
                // Cek apakah ada record round 1 yang keliru terbuat sebelumnya
                $wrongRound1 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
                if ($wrongRound1) {
                    $wrongRound1->update([
                        'round'      => 2,
                        'stage_type' => 'GARANSI',
                    ]);
                    $updatedStages++;
                } else {
                    CxAfterConfirmation::create([
                        'work_order_id' => $order->id,
                        'round'         => 2,
                        'stage_type'    => 'GARANSI',
                        'entered_at'    => $order->finished_date ?? $order->entry_date ?? $order->created_at ?? now(),
                    ]);
                    $syncedGaransiRound2++;
                }
            } elseif ($existing->stage_type !== 'GARANSI') {
                $existing->update(['stage_type' => 'GARANSI']);
                $updatedStages++;
            }
        }

        // Cek juga dari tabel work_order_warranties
        $warranties = WorkOrderWarranty::whereNotNull('garansi_spk_number')->get();
        foreach ($warranties as $w) {
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
                        $updatedStages++;
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
                    $updatedStages++;
                }
            }
        }

        // 2. SINKRONISASI REVISI (TERBITKAN AFTER KE-2)
        $this->info("⏳ 2. Memeriksa Riwayat Revisi Selesai untuk After Ke-2...");
        
        $finishedRevisions = WorkOrderRevision::where('status', 'FINISHED')
            ->with('workOrder')
            ->get();

        foreach ($finishedRevisions as $rev) {
            $order = $rev->workOrder;
            if (!$order) continue;

            $isOrderGr = str_contains((string)$order->spk_number, '-GR') || str_contains((string)$order->spk_number, 'GR-');
            $isOrderRnd = str_starts_with((string)$order->spk_number, 'RD-') || in_array(strtoupper(trim((string)$order->priority)), ['R&D', 'RND']);
            $isOrderOto = !empty($order->has_active_oto);
            $stage1 = $isOrderGr ? 'GARANSI' : ($isOrderRnd ? 'RND' : ($isOrderOto ? 'OTO' : 'REGULER'));

            // Pastikan After 1 ada
            $after1 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$after1) {
                $after1 = CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => $stage1,
                    'entered_at'    => $order->finished_date ?? $order->created_at ?? now(),
                ]);
            }

            // Pastikan After 2 terbit
            $after2 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 2)->first();
            if (!$after2) {
                CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 2,
                    'stage_type'    => 'REVISI',
                    'entered_at'    => $rev->finished_at ?? $rev->updated_at ?? now(),
                ]);
                $syncedRevisiRound2++;
            }
        }

        // 3. SINKRONISASI SPK OTO
        $this->info("⏳ 3. Memeriksa SPK OTO (has_active_oto)...");
        $otoOrders = WorkOrder::where('has_active_oto', 1)
            ->whereIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR, WorkOrderStatus::HISTORY])
            ->get();

        foreach ($otoOrders as $order) {
            $existing = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$existing) {
                CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => 'OTO',
                    'entered_at'    => $order->finished_date ?? $order->entry_date ?? $order->created_at ?? now(),
                ]);
                $syncedOto++;
            } elseif ($existing->stage_type !== 'OTO' && $existing->stage_type === 'REGULER') {
                $existing->update(['stage_type' => 'OTO']);
                $updatedStages++;
            }
        }

        // 4. SINKRONISASI SPK R&D
        $this->info("⏳ 4. Memeriksa SPK R&D (RD-... / priority = R&D / RND)...");
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
            } elseif ($existing->stage_type !== 'RND' && in_array($existing->stage_type, ['REGULER', 'GARANSI'])) {
                $existing->update(['stage_type' => 'RND']);
                $updatedStages++;
            }
        }

        // 5. RAPATKAN DAN KOREKSI DATA EKSISTING
        $this->info("⏳ 5. Merapikan dan mengoreksi data eksisting...");
        $allAfters = CxAfterConfirmation::with('workOrder')->get();
        foreach ($allAfters as $aft) {
            $wo = $aft->workOrder;
            if (!$wo) continue;

            $isGr = str_contains((string)$wo->spk_number, '-GR') || str_contains((string)$wo->spk_number, 'GR-');
            $isRnd = str_starts_with((string)$wo->spk_number, 'RD-') || in_array(strtoupper(trim((string)$wo->priority)), ['R&D', 'RND']);
            $isOto = !empty($wo->has_active_oto);

            if ($isGr) {
                // SPK Garansi WAJIB stage_type = GARANSI dan round = 2
                if ($aft->stage_type !== 'GARANSI' || (int)$aft->round !== 2) {
                    $aft->update([
                        'stage_type' => 'GARANSI',
                        'round'      => 2,
                    ]);
                    $updatedStages++;
                }
            } elseif ($aft->stage_type === 'GARANSI' && !$isGr) {
                // Salah terdeteksi Garansi hanya karena flag is_warranty
                if ($isRnd) {
                    $aft->update(['stage_type' => 'RND', 'round' => 1]);
                } elseif ($isOto) {
                    $aft->update(['stage_type' => 'OTO', 'round' => 1]);
                } else {
                    $aft->update(['stage_type' => 'REGULER', 'round' => 1]);
                }
                $updatedStages++;
            } elseif ($aft->stage_type === 'REGULER') {
                if ($isRnd) {
                    $aft->update(['stage_type' => 'RND']);
                    $updatedStages++;
                } elseif ($isOto) {
                    $aft->update(['stage_type' => 'OTO']);
                    $updatedStages++;
                }
            }
        }

        $this->newLine();
        $this->info("✅ SINKRONISASI SELESAI!");
        $this->table(
            ['Kategori Sinkronisasi', 'Jumlah Diproses'],
            [
                ['SPK Garansi (-GR) Masuk (Round 2 / After 2)', $syncedGaransiRound2],
                ['After Ke-2 Pasca Revisi Diterbitkan (Round 2)', $syncedRevisiRound2],
                ['SPK OTO Baru Masuk (Round 1)', $syncedOto],
                ['SPK R&D Baru Masuk (Round 1)', $syncedRnd],
                ['Record Eksisting Dikoreksi Label & Round-nya', $updatedStages],
                ['Total Record cx_after_confirmations Sekarang', CxAfterConfirmation::count()]
            ]
        );

        return Command::SUCCESS;
    }
}
