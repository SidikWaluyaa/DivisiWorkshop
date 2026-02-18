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
    protected $signature = 'generate:finish {--sync : Run synchronously instead of dispatching to queue}';

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
        $sync = $this->option('sync');

        $orders = WorkOrder::where('status', WorkOrderStatus::SELESAI->value)->get();
        $count = $orders->count();

        if ($count === 0) {
            $this->warn('No finished orders found.');
            return;
        }

        if ($sync) {
            $this->info("Regenerating {$count} reports synchronously...");
            $service = app(\App\Services\PhotoReportService::class);
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();

            foreach ($orders as $order) {
                /** @var WorkOrder $order */
                try {
                    $service->generateFinishReport($order);
                } catch (\Exception $e) {
                    $this->error("\nFailed for SPK {$order->spk_number}: " . $e->getMessage());
                }
                $progressBar->advance();
            }
            $progressBar->finish();
        } else {
            $this->info("Dispatched {$count} orders for report generation to queue.");
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();

            foreach ($orders as $order) {
                GeneratePhotoReportJob::dispatch($order);
                $progressBar->advance();
            }
            $progressBar->finish();
        }

        $this->newLine();
        $this->success("Finished " . ($sync ? "regenerating" : "dispatching") . " {$count} reports!");
    }

    /**
     * Helper for success message
     */
    private function success($message)
    {
        $this->line("<info>$message</info>");
    }
}
