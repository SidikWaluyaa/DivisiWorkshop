<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxIssue;
use App\Models\WorkOrder;
use App\Models\WorkOrderService;
use Carbon\Carbon;

class CxDebugCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cx:debug-upsell {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug CX Dashboard metrics to find why an SPK is missing from the upsell list.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateInput = $this->argument('date') ?: Carbon::now()->format('Y-m-d');
        $start = Carbon::parse($dateInput)->startOfDay();
        $end = Carbon::parse($dateInput)->endOfDay();

        $this->info("CX DEBUGGER: Analisis Upsell Tanggal " . $dateInput);
        $this->line("--------------------------------------------------");

        // 1. Find all Resolved Issues in this period
        $issues = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->get();

        $this->info("Total Resolved Issues hari ini: " . $issues->count());

        foreach ($issues as $issue) {
            $wo = $issue->workOrder;
            if (!$wo) {
                $this->error("Issue ID " . $issue->id . " tidak memiliki Work Order terkait.");
                continue;
            }
            
            $this->comment("\nSPK: " . $wo->spk_number);
            $this->line("  - Issue ID: " . $issue->id);
            $this->line("  - Tiket Dibuka: " . $issue->created_at);
            $this->line("  - Tiket Selesai: " . $issue->resolved_at);
            
            $services = $wo->workOrderServices;
            $this->line("  - Total Item Layanan di SPK: " . $services->count());
            
            $upsellFound = false;

                $name = $s->custom_service_name ?? $s->category_name;
                $isAfterIssue = $s->created_at >= $issue->created_at;
                
                // KEYWORD MATCH CHECK
                $inResolution = false;
                if (!empty($issue->resolution_notes)) {
                    $inResolution = (strpos(strtolower($issue->resolution_notes), strtolower($s->category_name)) !== false) ||
                                     (strpos(strtolower($issue->resolution_notes), strtolower($s->custom_service_name)) !== false);
                }

                $this->line("    * Service: " . $name . " (Price: " . number_format($s->cost, 0) . ")");
                $this->line("      > Waktu Input: " . $s->created_at);
                $this->line("      > Final Jawaban: " . ($issue->resolution_notes ?: "(Kosong)"));
                $this->line("      > Nama Jasa di Final Jawaban: " . ($inResolution ? "ADA (Lolos)" : "TIDAK ADA (Dibuang)"));
                
                if ($isAfterIssue && $inResolution) {
                    $this->info("      [STATUS: LOLOS - Terhitung sebagai Upsell CX]");
                    $upsellFound = true;
                } else {
                    $reasons = [];
                    if (!$isAfterIssue) $reasons[] = "WAKTU (< Tiket Dibuka)";
                    if (!$inResolution) $reasons[] = "TIDAK ADA DI FINAL JAWABAN (Dianggap Jasa Awal)";
                    $this->error("      [STATUS: DIBUANG - Alasan: " . implode(', ', $reasons) . "]");
                }

            if (!$upsellFound) {
                $this->warn("  [KESIMPULAN: SPK ini tidak dianggap sebagai Upsell karena tak ada satupun layanan tambahan yang memenuhi kriteria CX]");
            } else {
                $this->info("  [KESIMPULAN: SPK ini masuk hitungan Upsell Dashboard]");
            }
        }

        $this->line("\n--------------------------------------------------");
        $this->info("Gunakan 'php artisan cx:debug-upsell YYYY-MM-DD' untuk tanggal lain.");
    }
}
