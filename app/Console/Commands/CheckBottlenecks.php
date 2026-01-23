<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlgorithmManagement\LoadBalancingService;

class CheckBottlenecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'algorithm:bottlenecks
                            {--alert : Send alerts for critical bottlenecks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for bottlenecks and generate recommendations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('⚖️  Analyzing Load Distribution...');

        $service = new LoadBalancingService();
        $analysis = $service->analyze();

        // Display workload
        $this->newLine();
        $this->info('📊 Current Workload by Station:');
        $this->table(
            ['Station', 'Queue Length', 'Status'],
            [
                ['Assessment', $analysis['workload']['assessment'], $this->getStatusEmoji($analysis['workload']['assessment'])],
                ['Preparation', $analysis['workload']['preparation'], $this->getStatusEmoji($analysis['workload']['preparation'])],
                ['Sortir', $analysis['workload']['sortir'], $this->getStatusEmoji($analysis['workload']['sortir'])],
                ['Production', $analysis['workload']['production'], $this->getStatusEmoji($analysis['workload']['production'])],
                ['QC', $analysis['workload']['qc'], $this->getStatusEmoji($analysis['workload']['qc'])],
            ]
        );

        // Display health status
        $this->newLine();
        $healthEmoji = match($analysis['health_status']) {
            'healthy' => '🟢',
            'warning' => '🟡',
            'critical' => '🔴',
            default => '⚪',
        };
        $this->info(sprintf('%s Overall Health: %s', $healthEmoji, strtoupper($analysis['health_status'])));

        // Display bottlenecks
        if (count($analysis['bottlenecks']) > 0) {
            $this->newLine();
            $this->warn('⚠️  Bottlenecks Detected:');
            
            foreach ($analysis['bottlenecks'] as $bottleneck) {
                $severityEmoji = $bottleneck['severity'] === 'critical' ? '🔴' : '🟡';
                $this->line(sprintf(
                    '  %s %s: %d orders (%s)',
                    $severityEmoji,
                    ucfirst($bottleneck['station']),
                    $bottleneck['count'],
                    $bottleneck['severity']
                ));
            }

            // Display recommendations
            if (count($analysis['recommendations']) > 0) {
                $this->newLine();
                $this->info('💡 Recommendations:');
                foreach ($analysis['recommendations'] as $recommendation) {
                    $priorityEmoji = $recommendation['priority'] === 'high' ? '🔴' : '🟠';
                    $this->line(sprintf(
                        '  %s [%s] %s: %s',
                        $priorityEmoji,
                        strtoupper($recommendation['priority']),
                        ucfirst($recommendation['station']),
                        $recommendation['action']
                    ));
                }
            }

            // Send alerts if requested
            if ($this->option('alert')) {
                $criticalCount = collect($analysis['bottlenecks'])
                    ->where('severity', 'critical')
                    ->count();

                if ($criticalCount > 0) {
                    $this->warn(sprintf('🚨 %d critical bottleneck(s) detected - alerts should be sent!', $criticalCount));
                    // TODO: Implement alert notification (WhatsApp, Email, etc.)
                }
            }

        } else {
            $this->newLine();
            $this->info('✅ No bottlenecks detected - all stations operating normally!');
        }

        return Command::SUCCESS;
    }

    /**
     * Get status emoji based on queue length
     */
    protected function getStatusEmoji(int $count): string
    {
        if ($count >= 15) {
            return '🔴 Critical';
        } elseif ($count >= 10) {
            return '🟡 Warning';
        } else {
            return '🟢 Normal';
        }
    }
}
