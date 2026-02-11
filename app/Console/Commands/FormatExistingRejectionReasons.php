<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxIssue;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Log;

class FormatExistingRejectionReasons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:format-qc-rejection-reasons {--dry-run : Only show what would be updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Format existing QC rejection reasons to follow the new template (Upper, Sol, Kondisi Bawaan)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $template = "Upper : \nSol : \nKondisi Bawaan : ";
        $prefixes = ["Upper :", "Sol :", "Kondisi Bawaan :"];

        $this->info($dryRun ? "Running in DRY RUN mode..." : "Formatting rejection reasons...");

        // 1. Process CxIssue Descriptions
        $this->info("Processing CxIssue records...");
        $issues = CxIssue::all();
        $updatedIssues = 0;

        foreach ($issues as $issue) {
            /** @var CxIssue $issue */
            $current = $issue->description;
            
            // Skip if already formatted (crude check)
            if ($this->isAlreadyFormatted($current, $prefixes)) {
                continue;
            }

            $newDescription = "Upper : \nSol : \nKondisi Bawaan : " . trim($current);
            
            if ($dryRun) {
                $this->line("Would update CxIssue #{$issue->id} from: \"" . str_replace("\n", " ", substr($current, 0, 30)) . "...\" to formatted template.");
            } else {
                $issue->description = $newDescription;
                $issue->save();
            }
            $updatedIssues++;
        }

        // 2. Process WorkOrder Reception Rejection Reasons
        $this->info("Processing WorkOrder reception rejection reasons...");
        $orders = WorkOrder::whereNotNull('reception_rejection_reason')
            ->where('reception_rejection_reason', '!=', '')
            ->get();
        $updatedOrders = 0;

        foreach ($orders as $order) {
            /** @var WorkOrder $order */
            $current = $order->reception_rejection_reason;

            if ($this->isAlreadyFormatted($current, $prefixes)) {
                continue;
            }

            $newReason = "Upper : \nSol : \nKondisi Bawaan : " . trim($current);

            if ($dryRun) {
                $this->line("Would update WorkOrder #{$order->id} (SPK: {$order->spk_number}) from: \"" . str_replace("\n", " ", substr($current, 0, 30)) . "...\" to formatted template.");
            } else {
                $order->reception_rejection_reason = $newReason;
                $order->save();
            }
            $updatedOrders++;
        }

        $this->info("Summary:");
        $this->info("- CxIssues " . ($dryRun ? "to be updated" : "updated") . ": $updatedIssues");
        $this->info("- WorkOrders " . ($dryRun ? "to be updated" : "updated") . ": $updatedOrders");
        
        return 0;
    }

    /**
     * Check if the string already follows the template
     */
    private function isAlreadyFormatted($text, $prefixes)
    {
        if (empty($text)) return false;
        
        $hasAll = true;
        foreach ($prefixes as $prefix) {
            if (stripos($text, $prefix) === false) {
                $hasAll = false;
                break;
            }
        }
        return $hasAll;
    }
}
