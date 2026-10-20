<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxAfterConfirmation;
use App\Models\WorkOrder;
use App\Models\OTO;
use App\Models\WorkOrderRevision;
use App\Models\WorkOrderWarranty;

class CleanDuplicateAfterConfirmations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cx:clean-duplicate-after {--dry-run : Only preview records to be cleaned without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up invalid duplicate After 2/3 records on REGULER or RND work orders in cx_after_confirmations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = (bool)$this->option('dry-run');

        $this->info("🔍 Memeriksa duplikasi invalid After 2+ untuk SPK Reguler/RND...");

        // Cari semua record di cx_after_confirmations yang memiliki round >= 2 dengan stage REGULER atau RND
        $candidates = CxAfterConfirmation::where('round', '>=', 2)
            ->whereIn('stage_type', ['REGULER', 'RND'])
            ->with(['workOrder'])
            ->get();

        if ($candidates->isEmpty()) {
            $this->info("✅ Tidak ditemukan data duplikat After 2+ untuk SPK Reguler/RND. Data bersih!");
            return 0;
        }

        $this->warn("Ditemukan {$candidates->count()} kandidat record invalid After 2+ bertipe REGULER/RND:");

        $deletedCount = 0;

        foreach ($candidates as $record) {
            $wo = $record->workOrder;
            $spk = $wo?->spk_number ?? 'SPK #' . $record->work_order_id;

            // Verifikasi apakah SPK ini benar-benar tidak memiliki OTO, Revisi, atau Garansi sah
            $isGr = $wo ? (str_contains((string)$wo->spk_number, '-GR') || str_contains((string)$wo->spk_number, 'GR-')) : false;
            $hasCompletedOto = $wo ? OTO::where('work_order_id', $wo->id)->where('status', 'COMPLETED')->exists() : false;
            $hasFinishedRevisi = $wo ? WorkOrderRevision::where('work_order_id', $wo->id)->where('status', 'FINISHED')->exists() : false;

            if ($isGr || $hasCompletedOto || $hasFinishedRevisi) {
                // Ini mungkin stage-nya salah label (seharusnya OTO/REVISI/GARANSI), jangan dihapus sembarangan
                $this->line("⏩ Dilewati: {$spk} memiliki OTO/Revisi/Garansi sah (Stage saat ini: {$record->stage_type}).");
                continue;
            }

            // Pastikan ada After 1
            $hasRound1 = CxAfterConfirmation::where('work_order_id', $record->work_order_id)
                ->where('round', 1)
                ->exists();

            if (!$hasRound1) {
                $this->line("⏩ Dilewati: {$spk} tidak memiliki After 1.");
                continue;
            }

            $this->error("❌ [DUPLIKAT] ID: {$record->id} | SPK: {$spk} | Round: {$record->round} | Stage: {$record->stage_type} | Masuk: {$record->entered_at}");

            if (!$isDryRun) {
                $record->delete();
                $deletedCount++;
            }
        }

        if ($isDryRun) {
            $this->info("ℹ️ Mode Dry-Run: Tidak ada data yang dihapus. Jalankan tanpa flag --dry-run untuk mengeksekusi penghapusan.");
        } else {
            $this->info("🎉 Berhasil membersihkan {$deletedCount} record duplikat invalid dari tabel cx_after_confirmations!");
        }

        return 0;
    }
}
