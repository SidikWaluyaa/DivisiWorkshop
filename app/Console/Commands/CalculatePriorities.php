<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlgorithmManagement\PriorityCalculationService;

class CalculatePriorities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algorithm:priorities
                            {--show-distribution : Show priority distribution after calculation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate dynamic priority scores for all active orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚨 Starting Priority Calculation...');

        $service = new PriorityCalculationService();

        $startTime = microtime(true);
        $results = $service->calculateAll();
        $executionTime = (microtime(true) - $startTime) * 1000;

        $this->info('✅ Priority Calculation Complete!');
        $this->info(sprintf('📊 Updated %d orders in %.2fms', $results['updated'], $executionTime));

        // Show distribution if requested
        if ($this->option('show-distribution')) {
            $distribution = $service->getDistribution();
            
            $this->newLine();
            $this->info('📈 Priority Distribution:');
            $this->table(
                ['Priority Level', 'Count', 'Percentage'],
                [
                    [
                        '🔴 Critical (≥80)', 
                        $distribution['critical'], 
                        $this->calculatePercentage($distribution['critical'], $results['updated'])
                    ],
                    [
                        '🟠 High (60-79)', 
                        $distribution['high'], 
                        $this->calculatePercentage($distribution['high'], $results['updated'])
                    ],
                    [
                        '🟡 Medium (40-59)', 
                        $distribution['medium'], 
                        $this->calculatePercentage($distribution['medium'], $results['updated'])
                    ],
                    [
                        '🟢 Low (<40)', 
                        $distribution['low'], 
                        $this->calculatePercentage($distribution['low'], $results['updated'])
                    ],
                ]
            );
        }

        return Command::SUCCESS;
    }

    /**
     * Calculate percentage
     */
    protected function calculatePercentage(int $count, int $total): string
    {
        if ($total === 0) {
            return '0%';
        }
        return sprintf('%.1f%%', ($count / $total) * 100);
    }
}
