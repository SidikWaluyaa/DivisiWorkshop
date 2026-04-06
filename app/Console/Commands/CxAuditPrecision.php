<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxIssue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CxAuditPrecision extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cx:audit-precision {date?} {--fix : Perbaiki data secara otomatis di database} {--month : Audit untuk sebulan penuh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit presisi data CX untuk memastikan akurasi Upsell 100% di Dashboard.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->argument('date') ?: Carbon::now()->format('Y-m-d');
        $isMonth = $this->option('month');
        $isFix = $this->option('fix');

        if ($isMonth) {
            $date = Carbon::parse($input . '-01');
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();
            $label = $date->format('F Y');
        } else {
            $start = Carbon::parse($input)->startOfDay();
            $end = Carbon::parse($input)->endOfDay();
            $label = $input;
        }

        $this->info("\n🕵️‍♂️ AUDIT PRESISI CX: {$label}");
        $this->line("=================================================");

        // 1. Ambil semua isu yang resolved (tanpa filter BATAL dulu agar transparan)
        $issues = CxIssue::where('status', 'RESOLVED')
            ->whereBetween('resolved_at', [$start, $end])
            ->with(['workOrder.workOrderServices'])
            ->get();

        if ($issues->isEmpty()) {
            $this->warn("Tidak ditemukan data resolusi pada periode ini.");
            return;
        }

        $headers = ['ID', 'SPK', 'Type', 'WO Status', 'Jasa Baru', 'Nominal', 'Status Audit'];
        $rows = [];
        $totalNominal = 0;
        $mismatchCount = 0;

        foreach ($issues as $i) {
            $wo = $i->workOrder;
            $woStatus = $wo ? (is_object($wo->status) ? $wo->status->value : $wo->status) : 'MISSING';
            
            // Logika Smart Detection (Harus sinkron dengan DashboardService)
            $servicesAfterIssue = $wo ? $wo->workOrderServices->where('created_at', '>=', $i->created_at)
                ->filter(function($s) use ($i, $wo) {
                    if (!empty($s->custom_service_name) && str_starts_with($s->custom_service_name, 'OTO:')) return false;
                    
                    $notes = strtolower($i->resolution_notes);
                    $techNotes = $wo ? strtolower($wo->technician_notes) : '';
                    $cat = strtolower($s->category_name);
                    $custom = strtolower($s->custom_service_name);
                    
                    // Stop words for matching
                    $stopWords = ['ganti', 'tambah', 'pasang', 'repair', 'jasa', 'service', 'dan', 'pada', 'bagian', 'standar'];
                    
                    $match = false;
                    
                    // Function to check match in a target note
                    $checkMatch = function($targetNote) use ($stopWords, $cat, $custom) {
                        if (empty($targetNote)) return false;
                        $noteWords = preg_split('/[\s,\+\.]+/', $targetNote, -1, PREG_SPLIT_NO_EMPTY);
                        foreach($noteWords as $nw) {
                            if (in_array($nw, $stopWords)) continue;
                            if (strlen($nw) > 2) {
                                if (!empty($cat) && (str_contains($cat, $nw) || str_contains($nw, $cat))) return true;
                                if (!empty($custom) && (str_contains($custom, $nw) || str_contains($nw, $custom))) return true;
                                
                                // Specific cases
                                if ($nw === 'midosle' && str_contains($cat, 'midsole')) return true;
                                if ($nw === 'lapkul' && (str_contains($cat, 'lapis') || str_contains($cat, 'kulit'))) return true;
                            }
                        }
                        return false;
                    };

                    // DUAL-CHECK: Check resolution notes OR tech notes
                    $match = $checkMatch($notes) || $checkMatch($techNotes);
                    
                    return $match;
                }) : collect();

            $revenue = $servicesAfterIssue->sum('cost');
            $hasServices = $servicesAfterIssue->count() > 0;
            $type = $i->resolution_type ?: 'NULL';

            $auditStatus = "OK";
            if ($woStatus !== 'BATAL') {
                if ($hasServices && $type !== 'tambah_jasa') {
                    $auditStatus = "⚠️ MISMATCH (Sembunyi)";
                    $mismatchCount++;
                    
                    if ($isFix) {
                        DB::table('cx_issues')->where('id', $i->id)->update(['resolution_type' => 'tambah_jasa']);
                        $auditStatus = "✅ FIXED";
                        $type = 'tambah_jasa';
                    }
                } elseif (!$hasServices && $type === 'tambah_jasa') {
                    $auditStatus = "❓ GHOST UPSELL"; // Tombol TJ tapi tak ada jasa baru
                }
            } else {
                $auditStatus = "🚫 CANCELLED";
            }

            if ($type === 'tambah_jasa' && $woStatus !== 'BATAL') {
                $totalNominal += $revenue;
            }

            $serviceNames = $servicesAfterIssue->map(function($s) {
                return ($s->custom_service_name ?: $s->category_name) . " (Rp " . number_format($s->cost) . ")";
            })->implode("\n");

            $rows[] = [
                $i->id,
                $wo ? $wo->spk_number : 'N/A',
                $type,
                $woStatus,
                $servicesAfterIssue->count() . " Jasa\n---\n" . $serviceNames,
                "Rp " . number_format($revenue),
                $auditStatus
            ];
        }

        $this->table($headers, $rows);

        $this->line("\n=================================================");
        $this->info("SUMMARY AUDIT:");
        $this->line("- Total Resolved: " . $issues->count());
        $this->line("- Total Mismatch: " . $mismatchCount);
        $this->info("- TOTAL NOMINAL (UPSELL): Rp " . number_format($totalNominal));
        
        if ($mismatchCount > 0 && !$isFix) {
            $this->warn("\n[!] Ditemukan {$mismatchCount} data yang tidak sinkron.");
            $this->warn("Gunakan flag '--fix' untuk otomatis menyinkronkan dengan Dashboard.");
        }

        if ($isFix) {
            $this->info("\n[SUCCESS] Sinkronisasi selesai! Dashboard kini 100% akurat.");
        }
    }
}
