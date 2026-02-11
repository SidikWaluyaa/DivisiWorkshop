<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CxIssue;

class FixServiceFormatting extends Command
{
    protected $signature = 'cx:fix-services';
    protected $description = 'Reformat existing service strings into numbered lists';

    public function handle()
    {
        $issues = CxIssue::whereNotNull('suggested_services')
            ->orWhereNotNull('recommended_services')
            ->get();

        $count = 0;
        foreach ($issues as $issue) {
            $updated = false;

            // Fix Suggested Services
            if ($issue->suggested_services && !str_starts_with($issue->suggested_services, '1.')) {
                $items = explode(',', $issue->suggested_services);
                if (count($items) > 0) {
                    $issue->suggested_services = collect($items)
                        ->map(fn($item, $idx) => ($idx + 1) . ". " . trim($item))
                        ->implode("\n");
                    $updated = true;
                }
            }

            // Fix Recommended Services
            if ($issue->recommended_services && !str_starts_with($issue->recommended_services, '1.')) {
                $items = explode(',', $issue->recommended_services);
                if (count($items) > 0) {
                    $issue->recommended_services = collect($items)
                        ->map(fn($item, $idx) => ($idx + 1) . ". " . trim($item))
                        ->implode("\n");
                    $updated = true;
                }
            }

            if ($updated) {
                $issue->save();
                $count++;
            }
        }

        $this->info("Successfully reformatted {$count} issues.");
    }
}
