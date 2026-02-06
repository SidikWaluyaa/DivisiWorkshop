<?php

namespace App\Console\Commands;

use App\Models\CsLead;
use App\Models\CsActivity;
use App\Models\User;
use Illuminate\Console\Command;

class FixLeadAssignment extends Command
{
    protected $signature = 'cs:fix-lead-assignment {--dry-run : Preview changes without applying}';
    protected $description = 'Fix lead cs_id assignment based on creator from first activity';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Scanning leads for assignment issues...');
        
        $fixed = 0;
        $skipped = 0;
        
        // Get all leads that might need fixing
        $leads = CsLead::whereIn('status', [
            CsLead::STATUS_GREETING,
            CsLead::STATUS_KONSULTASI,
            CsLead::STATUS_CLOSING
        ])->get();
        
        foreach ($leads as $lead) {
            /** @var CsLead $lead */
            $firstActivity = CsActivity::where('cs_lead_id', $lead->id)
                ->orderBy('created_at', 'asc')
                ->first();
            
            if (!$firstActivity) {
                $this->line("⏭ Lead #{$lead->id} - No activity found, skipping");
                $skipped++;
                continue;
            }
            
            $creatorId = $firstActivity->user_id;
            $creator = User::find($creatorId);
            
            if (!$creator) {
                $this->line("⏭ Lead #{$lead->id} - Creator user not found, skipping");
                $skipped++;
                continue;
            }
            
            // Skip if creator is admin/owner (they use load balancing intentionally)
            if ($creator->isAdmin() || $creator->isOwner()) {
                $this->line("⏭ Lead #{$lead->id} - Created by Admin/Owner, skipping");
                $skipped++;
                continue;
            }
            
            // Check if current cs_id matches creator
            if ($lead->cs_id === $creatorId) {
                $skipped++;
                continue; // Already correct
            }
            
            // Need to fix
            $currentCs = $lead->cs ? $lead->cs->name : 'NULL';
            $this->line("✏️ Lead #{$lead->id} ({$lead->customer_name}): {$currentCs} → {$creator->name}");
            
            if (!$dryRun) {
                $lead->update(['cs_id' => $creatorId]);
            }
            $fixed++;
        }
        
        $this->newLine();
        if ($dryRun) {
            $this->info("📊 DRY RUN complete: {$fixed} leads would be fixed, {$skipped} skipped");
            $this->info("Run without --dry-run to apply changes.");
        } else {
            $this->info("✅ Fixed {$fixed} leads, skipped {$skipped}");
        }
        
        return self::SUCCESS;
    }
}
