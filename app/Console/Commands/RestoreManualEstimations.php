<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Models\Invoice;
use App\Models\WorkOrderLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RestoreManualEstimations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recovery:restore-estimations {--dry-run : Only show what will be restored without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore lost manual estimation dates from WorkOrderLog action = ESTIMATION_UPDATED';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info("=========================================");
            $this->info("      DRY-RUN MODE: NO DATABASE WRITES   ");
            $this->info("=========================================");
        } else {
            $this->warn("=========================================");
            $this->warn("    PRODUCTION RUN: UPDATING DATABASE    ");
            $this->warn("=========================================");
        }

        // Fetch logs sorted from oldest to newest so the latest change overrides previous ones
        $logs = WorkOrderLog::whereIn('action', ['ESTIMATION_UPDATED', 'REPORT_ISSUE'])
            ->orderBy('created_at', 'asc')
            ->get();

        if ($logs->isEmpty()) {
            $this->error("No ESTIMATION_UPDATED or REPORT_ISSUE logs found in the database.");
            return 0;
        }

        $this->info("Found " . $logs->count() . " logs to analyze...");

        $restoredWorkOrders = 0;
        $restoredInvoices = 0;
        $workOrdersToProcess = [];

        foreach ($logs as $log) {
            $description = $log->description;
            $workOrderId = $log->work_order_id;
            $detectedDate = null;

            // Pattern 1: Admin mengubah estimasi pengerjaan dari X ke DD/MM/YYYY
            if (preg_match('/ke\s+(\d{2})\/(\d{2})\/(\d{4})/', $description, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                $detectedDate = "$year-$month-$day";
            } 
            // Pattern 2: Admin mengubah estimasi pengerjaan dari X ke YYYY-MM-DD
            elseif (preg_match('/ke\s+(\d{4})-(\d{2})-(\d{2})/', $description, $matches)) {
                $detectedDate = $matches[1];
            }
            // Pattern 3: Reported Issue (OVERLOAD): Request ubah estimasi selesai menjadi: YYYY-MM-DD
            elseif (preg_match('/estimasi selesai menjadi:\s*(\d{4}-\d{2}-\d{2})/i', $description, $matches)) {
                $detectedDate = $matches[1];
            }

            if ($detectedDate) {
                try {
                    $carbonDate = Carbon::parse($detectedDate);
                    // Keep the latest date for each work order
                    $workOrdersToProcess[$workOrderId] = [
                        'date' => $carbonDate->format('Y-m-d'),
                        'log_id' => $log->id,
                        'log_date' => $log->created_at->toDateTimeString(),
                        'description' => $description
                    ];
                } catch (\Exception $e) {
                    $this->error("Failed to parse date '$detectedDate' from log ID: {$log->id}");
                }
            }
        }

        $this->info("Unique Work Orders with logged manual estimations: " . count($workOrdersToProcess));
        $this->info("--------------------------------------------------------------------------------");

        foreach ($workOrdersToProcess as $woId => $info) {
            $workOrder = WorkOrder::with('invoice')->find($woId);

            if (!$workOrder) {
                $this->warn("WorkOrder ID #{$woId} not found in database (possibly hard deleted). Skipping.");
                continue;
            }

            $currentWoDate = $workOrder->estimation_date ? $workOrder->estimation_date->format('Y-m-d') : 'NULL';
            $targetDate = $info['date'];

            // Log details of what we found
            $this->line("SPK: <comment>{$workOrder->spk_number}</comment> | Cust: <comment>{$workOrder->customer_name}</comment>");
            $this->line("  - Log Date: {$info['log_date']} (Log ID: {$info['log_id']})");
            $this->line("  - Log Text: \"{$info['description']}\"");
            $this->line("  - Current Date in DB: <error>{$currentWoDate}</error> | Target Restore Date: <info>{$targetDate}</info>");

            $invoice = $workOrder->invoice;
            if ($invoice) {
                $currentInvDate = $invoice->estimasi_selesai ? $invoice->estimasi_selesai->format('Y-m-d') : 'NULL';
                $this->line("  - Linked Invoice: <comment>{$invoice->invoice_number}</comment> (Current Date in DB: <error>{$currentInvDate}</error>)");
            }

            // Perform restore
            if (!$dryRun) {
                DB::transaction(function () use ($workOrder, $invoice, $targetDate, &$restoredWorkOrders, &$restoredInvoices) {
                    // Update Work Order
                    $workOrder->update([
                        'estimation_date' => $targetDate,
                        'is_manual_estimasi' => true // Seal the lock!
                    ]);
                    $restoredWorkOrders++;

                    // Update Invoice
                    if ($invoice) {
                        $invoice->update([
                            'estimasi_selesai' => $targetDate,
                            'is_manual_estimasi' => true // Seal the lock!
                        ]);
                        $restoredInvoices++;
                    }
                });
                $this->line("  <info>-> SUCCESS: Restored and locked estimasi to {$targetDate}</info>");
            } else {
                $this->line("  <comment>-> DRY-RUN: Would restore and lock estimasi to {$targetDate}</comment>");
            }
            $this->line("--------------------------------------------------------------------------------");
        }

        $this->info("=========================================");
        if ($dryRun) {
            $this->info("Dry-run complete. Checked " . count($workOrdersToProcess) . " orders.");
        } else {
            $this->info("Restore complete! Restored & locked {$restoredWorkOrders} Work Orders and {$restoredInvoices} Invoices.");
        }
        $this->info("=========================================");

        return 0;
    }
}
