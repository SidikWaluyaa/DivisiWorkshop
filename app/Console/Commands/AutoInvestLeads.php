<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CsLead;
use Carbon\Carbon;

class AutoInvestLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cs:auto-invest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically move stale leads to Invest Greeting or Invest Consultation status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Auto Invest Check...');

        // 1. Check NEW -> INVEST_GREETING (Older than 24 Hours)
        // Rule: "1x24 jam setelah nomor hp tersebut diinput" -> created_at
        $newLeads = CsLead::where('status', CsLead::STATUS_NEW)
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        $countNew = 0;
        foreach ($newLeads as $lead) {
            $lead->update([
                'status' => CsLead::STATUS_INVEST_GREETING,
                'last_updated_at' => now(), // Update active timestamp
            ]);
            $countNew++;
            $this->line("Lead #{$lead->id} ({$lead->customer_name}) moved to INVEST_GREETING.");
        }

        // 2. Check CONSULTATION -> INVEST_KONSULTASI (Older than 3 Days from last update)
        $consultLeads = CsLead::where('status', CsLead::STATUS_KONSULTASI)
            ->where('last_updated_at', '<', Carbon::now()->subDays(3))
            ->get();

        $countConsult = 0;
        foreach ($consultLeads as $lead) {
            $lead->update([
                'status' => CsLead::STATUS_INVEST_KONSULTASI,
                'last_updated_at' => now(),
            ]);
            $countConsult++;
            $this->line("Lead #{$lead->id} ({$lead->customer_name}) moved to INVEST_KONSULTASI.");
        }

        $this->info("Completed. Moved {$countNew} leads to Invest Greeting and {$countConsult} leads to Invest Consultation.");
    }
}
