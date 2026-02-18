<?php

namespace App\Console\Commands;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MigrateReportUrls extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'report:migrate-urls';

    /**
     * The console command description.
     */
    protected $description = 'Migrate finish_report_url from PDF format to Digital Landing Page format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders = WorkOrder::whereIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::DIANTAR])->get();

        $this->info("Found " . $orders->count() . " orders to migrate.");

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        foreach ($orders as $order) {
            // 1. Ensure token exists
            if (empty($order->invoice_token)) {
                $order->invoice_token = (string) Str::uuid();
            }

            // 2. Set the new Landing Page URL
            $order->finish_report_url = route('customer.report', $order->invoice_token);
            $order->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Migration completed successfully!');
    }
}
