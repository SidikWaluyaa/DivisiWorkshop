<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ========================================
// ALGORITHM AUTOMATION SCHEDULES
// ========================================

// Auto-Assignment: Assign orders to technicians every 5 minutes
Schedule::command('algorithm:auto-assign')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Auto-assignment completed successfully');
    })
    ->onFailure(function () {
        Log::error('Auto-assignment failed');
    });

// Priority Calculation: Update priority scores every 10 minutes
Schedule::command('algorithm:priorities')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Priority calculation completed successfully');
    });

// Bottleneck Detection: Check for bottlenecks every 15 minutes with alerts
Schedule::command('algorithm:bottlenecks --alert')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->onSuccess(function () {
        Log::info('Bottleneck check completed successfully');
    });

// Metrics Cleanup: Clean old metrics (older than 30 days) weekly
Schedule::call(function () {
    \App\Models\AlgorithmMetric::where('recorded_at', '<', now()->subDays(30))->delete();
    Log::info('Old algorithm metrics cleaned up');
})->weekly()->sundays()->at('02:00');
