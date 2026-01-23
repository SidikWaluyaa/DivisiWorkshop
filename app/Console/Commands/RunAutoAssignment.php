<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlgorithmManagement\AutoAssignmentService;

class RunAutoAssignment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algorithm:auto-assign
                            {--force : Force run even if algorithm is disabled}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run auto-assignment algorithm to assign orders to technicians';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Starting Auto-Assignment Algorithm...');

        // Force run if --force flag is provided
        if ($this->option('force')) {
            $this->warn('⚠️  Force mode enabled - running regardless of config status');
            
            // Enable algorithm via model BEFORE creating service
            \App\Models\AlgorithmConfig::where('algorithm_name', 'auto_assignment')
                ->update(['is_active' => true]);
        }

        $service = new AutoAssignmentService();
        $results = $service->run();

        // Display results
        if (isset($results['status']) && $results['status'] === 'skipped') {
            $this->warn('⏭️  Algorithm skipped: ' . $results['reason']);
            return Command::FAILURE;
        }

        if (isset($results['error'])) {
            $this->error('❌ Error: ' . $results['error']);
            return Command::FAILURE;
        }

        $this->info('✅ Auto-Assignment Complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Assigned', $results['assigned'] ?? 0],
                ['Skipped', $results['skipped'] ?? 0],
                ['Failed', $results['failed'] ?? 0],
            ]
        );

        // Show success rate
        $total = ($results['assigned'] ?? 0) + ($results['skipped'] ?? 0) + ($results['failed'] ?? 0);
        if ($total > 0) {
            $successRate = (($results['assigned'] ?? 0) / $total) * 100;
            $this->info(sprintf('📊 Success Rate: %.1f%%', $successRate));
        }

        return Command::SUCCESS;
    }
}
