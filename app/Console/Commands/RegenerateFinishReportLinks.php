<?php

namespace App\Console\Commands;

use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RegenerateFinishReportLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:regenerate-links {--all : Regenerate links for all finished orders, even if they already have a landing page link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate old PDF report links to the new secure Digital Landing Page URLs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting report link regeneration...');

        $query = WorkOrder::whereIn('status', [
            WorkOrderStatus::SELESAI,
            WorkOrderStatus::DIANTAR
        ]);

        if (!$this->option('all')) {
            // Only target records that have a PDF link or no link at all
            $query->where(function ($q) {
                $q->where('finish_report_url', 'like', '%.pdf')
                  ->orWhereNull('finish_report_url')
                  ->orWhere('finish_report_url', '');
            });
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->warn('No orders found requiring link regeneration.');
            return;
        }

        $bar = $this->output->createProgressBar($orders->count());
        $bar->start();

        /** @var \App\Models\WorkOrder $order */
        foreach ($orders as $order) {
            // 1. Ensure invoice_token exists
            if (empty($order->invoice_token)) {
                $order->invoice_token = Str::uuid()->toString();
            }

            // 2. Generate new route-based URL
            $newUrl = route('customer.report', [
                'spk' => Str::slug($order->spk_number),
                'token' => $order->invoice_token
            ]);

            // 3. Update the URL
            $order->finish_report_url = $newUrl;
            $order->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully regenerated links for {$orders->count()} orders.");
    }
}
