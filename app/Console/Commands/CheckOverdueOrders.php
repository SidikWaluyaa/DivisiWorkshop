<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckOverdueOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue orders and move to donation or send reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting Finance Overdue Check...");
        
        // Scope: Active orders with remaining debt
        // Exclude Cancelled, Already Donation, or Fully Paid
        $orders = \App\Models\WorkOrder::where('sisa_tagihan', '>', 0)
            ->whereNotIn('status', [\App\Enums\WorkOrderStatus::BATAL, \App\Enums\WorkOrderStatus::DONASI])
            ->where(function($q) {
                $q->where('status_pembayaran', '!=', 'L') // Ensure not Paid
                  ->orWhereNull('status_pembayaran');
            })
            ->get();

        $donationCount = 0;
        $reminderCount = 0;

        foreach ($orders as $order) {
            
            // 1. Determine Effective Due Date
            $dueDate = null;
            $triggerType = 'MANUAL';

            if ($order->payment_due_date) {
                // Priority 1: User Manual Input
                $dueDate = $order->payment_due_date;
            } else {
                // Priority 2: Automatic Logic
                if ($order->status === \App\Enums\WorkOrderStatus::SELESAI) {
                    // Logic: Pelunasan/After (30 days after finish)
                    if ($order->finished_date) {
                        $dueDate = $order->finished_date->copy()->addDays(30);
                        $triggerType = 'FINISHED_AUTO';
                    }
                } else {
                    // Logic: DP/Before (30 days after entry)
                    if ($order->created_at) {
                        $dueDate = $order->created_at->copy()->addDays(30);
                        $triggerType = 'ENTRY_AUTO';
                    }
                }
            }

            if (!$dueDate) continue;

            $now = now()->startOfDay();
            $target = $dueDate->copy()->startOfDay();
            
            // Calculate: Target - Now. 
            // Result: Positive = Future (H-X), Negative = Past (Overdue), 0 = Today
            $daysUntilDue = $now->diffInDays($target, false); 

            // 2. CHECK FOR DONATION (Overdue > 1 days)
            // If daysUntilDue is -2 or less (meaning overdue by 2 days or more)
            if ($daysUntilDue < -1) { 
                $this->moveToDonation($order, $dueDate);
                $donationCount++;
                continue;
            }

            // 3. CHECK FOR REMINDERS (H-7, H-3, H-0)
            // Expect Positive: 7, 3, 0
            if (in_array((int)$daysUntilDue, [7, 3, 0])) {
                $this->sendReminder($order, $daysUntilDue, $dueDate);
                $reminderCount++;
            }
        }

        $this->info("Done. Moved to Donation: $donationCount. Reminders Sent: $reminderCount.");
    }

    private function moveToDonation($order, $dueDate)
    {
        $order->update([
            'status' => \App\Enums\WorkOrderStatus::DONASI,
            'donated_at' => now(),
            'notes' => $order->notes . "\n[AUTO-SYSTEM] Dipindahkan ke status DONASI pada " . now()->format('d/m/Y') . " karena melewati jatuh tempo (" . $dueDate->format('d/m/Y') . ")."
        ]);
        
        $this->warn("Order {$order->spk_number} moved to DONATION.");
    }

    private function sendReminder($order, $daysUntilDue, $dueDate)
    {
        // Don't spam: check if already sent TODAY
        if ($order->last_reminder_at && $order->last_reminder_at->isToday()) {
            return;
        }

        // Integration point with WhatsApp Controller
        // For now, we simulate sending
        
        $msgType = $daysUntilDue === 0 ? "HARI INI" : "dalam " . abs($daysUntilDue) . " hari";
        $message = "Halo {$order->customer_name}, ini pengingat pembayaran sepatu Anda ({$order->spk_number}). Jatuh tempo pembayaran adalah $msgType ({$dueDate->format('d/m/Y')}). Mohon segera diselesaikan agar status data tidak hangus/donasi.";

        // In real app: WhatsAppService::send($order->customer_phone, $message);
        
        // Update Counter
        $order->increment('reminder_count');
        $order->update(['last_reminder_at' => now()]);

        $this->line("Reminder sent to {$order->spk_number} (Due: {$dueDate->format('Y-m-d')})");
    }
}
