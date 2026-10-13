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

        $syncedGaransi = 0;
        $syncedRevisiRound2 = 0;
        $syncedOto = 0;
        $syncedRnd = 0;
        $updatedStages = 0;

        // 1. SINKRONISASI SPK GARANSI (-GR & is_warranty)
        $this->info("⏳ 1. Memeriksa SPK Garansi (-GR & is_warranty)...");
        
        // Ambil semua work orders garansi yang sudah SELESAI
        $garansiOrders = WorkOrder::where(function ($q) {
            $q->where('is_warranty', 1)
              ->orWhere('spk_number', 'like', '%-GR%')
              ->orWhere('spk_number', 'like', '%-GR')
              ->orWhere('category_spk', 'like', '%Garansi%');
        })
        ->whereIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR, WorkOrderStatus::HISTORY])
        ->get();

        foreach ($garansiOrders as $order) {
            $existing = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$existing) {
                CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => 'GARANSI',
                    'entered_at'    => $order->finished_date ?? $order->entry_date ?? $order->created_at ?? now(),
                ]);
                $syncedGaransi++;
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
                $existing = CxAfterConfirmation::where('work_order_id', $reworkWo->id)->where('round', 1)->first();
                if (!$existing) {
                    CxAfterConfirmation::create([
                        'work_order_id' => $reworkWo->id,
                        'round'         => 1,
                        'stage_type'    => 'GARANSI',
                        'entered_at'    => $reworkWo->finished_date ?? $w->finished_at ?? $reworkWo->created_at ?? now(),
                    ]);
                    $syncedGaransi++;
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

            // Pastikan After 1 ada
            $after1 = CxAfterConfirmation::where('work_order_id', $order->id)->where('round', 1)->first();
            if (!$after1) {
                $after1 = CxAfterConfirmation::create([
                    'work_order_id' => $order->id,
                    'round'         => 1,
                    'stage_type'    => $order->is_warranty ? 'GARANSI' : 'REGULER',
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
        $this->info("⏳ 4. Memeriksa SPK R&D (priority = R&D / RND)...");
        $rndOrders = WorkOrder::whereIn('priority', ['R&D', 'RND'])
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
            } elseif ($existing->stage_type !== 'RND' && $existing->stage_type === 'REGULER') {
                $existing->update(['stage_type' => 'RND']);
                $updatedStages++;
            }
        }

        // 5. RAPATKAN DATA EKSISTING YANG BELUM TERDETEKSI
        $this->info("⏳ 5. Merapikan data eksisting...");
        $allAfters = CxAfterConfirmation::with('workOrder')->where('stage_type', 'REGULER')->get();
        foreach ($allAfters as $aft) {
            $wo = $aft->workOrder;
            if (!$wo) continue;

            $newStage = null;
            if ($wo->is_warranty || str_contains((string)$wo->spk_number, '-GR')) {
                $newStage = 'GARANSI';
            } elseif ($wo->has_active_oto) {
                $newStage = 'OTO';
            } elseif (in_array(strtoupper(trim((string)$wo->priority)), ['R&D', 'RND'])) {
                $newStage = 'RND';
            }

            if ($newStage && $newStage !== $aft->stage_type) {
                $aft->update(['stage_type' => $newStage]);
                $updatedStages++;
            }
        }

        $this->newLine();
        $this->info("✅ SINKRONISASI SELESAI!");
        $this->table(
            ['Kategori Sinkronisasi', 'Jumlah Diproses'],
            [
                ['SPK Garansi Baru Masuk (Round 1)', $syncedGaransi],
                ['After Ke-2 Pasca Revisi Diterbitkan (Round 2)', $syncedRevisiRound2],
                ['SPK OTO Baru Masuk (Round 1)', $syncedOto],
                ['SPK R&D Baru Masuk (Round 1)', $syncedRnd],
                ['Record Eksisting Dikoreksi Label Stage-nya', $updatedStages],
                ['Total Record cx_after_confirmations Sekarang', CxAfterConfirmation::count()]
            ]
        );

        return Command::SUCCESS;
    }
}
