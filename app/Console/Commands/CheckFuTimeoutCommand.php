<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Log;

class CheckFuTimeoutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workshop:check-fu-timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SPKs in Rak FU for 5-day timeout and lock them for Lead Workshop manual escalation (FR-9.4)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Rak FU 5-day timeout SPKs...');

        // Find SPKs currently in CX_FOLLOWUP status for > 5 days
        $thresholdDate = now()->subDays(5);

        $timedOutOrders = WorkOrder::where('status', WorkOrderStatus::CX_FOLLOWUP)
            ->where(function($q) use ($thresholdDate) {
                $q->where('updated_at', '<=', $thresholdDate)
                  ->orWhere('waktu', '<=', $thresholdDate);
            })
            ->get();

        $count = 0;
        foreach ($timedOutOrders as $order) {
            $order->current_location = 'Rak FU (Terkuci - Timeout 5 Hari Escalation Pak Dito)';
            $order->save();

            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => 1, // System
                'action' => 'RAK_FU_5DAY_TIMEOUT_LOCKED',
                'description' => 'SPK tertahan 5 hari di Rak FU tanpa respons customer. Otomatis dikunci & masuk antrean eskalasi Lead Workshop (Pak Dito).',
                'step' => 'RAK_FU'
            ]);

            $count++;
            $this->line("Locked SPK #{$order->spk_number} (ID: {$order->id}) for Lead Workshop escalation.");
        }

        $this->info("Completed. Total {$count} SPK locked for Lead Workshop escalation.");

        return Command::SUCCESS;
    }
}
