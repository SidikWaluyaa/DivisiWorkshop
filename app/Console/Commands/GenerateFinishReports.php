<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WorkOrder;
use App\Jobs\GeneratePhotoReportJob;
use App\Enums\WorkOrderStatus;

class GenerateFinishReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-finish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate premium PDF reports for all finished/completed work orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding finished orders to generate premium reports...');

        $orders = WorkOrder::where('status', WorkOrderStatus::SELESAI->value)->get();
        $count = $orders->count();

        if ($count === 0) {
            $this->warn('No finished orders found.');
            return;
        }

        $this->info("Dispatched {$count} orders for report generation.");

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        foreach ($orders as $order) {
            GeneratePhotoReportJob::dispatch($order->id);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->success("Finished dispatching {$count} reports to the queue!");
    }

    /**
     * Helper for success message
     */
    private function success($message)
    {
        $this->line("<info>$message</info>");
    }
}
