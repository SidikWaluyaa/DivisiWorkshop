<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxIssue;

class CleanupCxDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-cx-descriptions {--dry-run : Only show what would be cleaned}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove "QC Awal Gagal (Reception): " prefix from CX issue descriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $prefix = "QC Awal Gagal (Reception): ";

        $this->info("Cleaning up CX issue descriptions...");

        $issues = CxIssue::where('description', 'LIKE', $prefix . '%')->get();

        if ($issues->isEmpty()) {
            $this->info("No issues found with the specified prefix.");
            return;
        }

        foreach ($issues as $issue) {
            /** @var \App\Models\CxIssue $issue */
            $oldDescription = $issue->description;
            $newDescription = str_replace($prefix, "", $oldDescription);

            if ($dryRun) {
                $this->line("Would cleanup: \"{$oldDescription}\" -> \"{$newDescription}\"");
            } else {
                $issue->description = $newDescription;
                $issue->save();
                $this->line("Cleaned: \"{$oldDescription}\" -> \"{$newDescription}\"");
            }
        }

        $this->info("Cleanup completed.");
    }
}
