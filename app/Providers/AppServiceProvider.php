<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Sidebar Badges View Composer
        \Illuminate\Support\Facades\View::composer('layouts.partials.sidebar-content', function ($view) {
            if (auth()->check()) {
                $counts = [
                    'cs' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::CX_FOLLOWUP)->count(),
                    'reception' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::SPK_PENDING)->count(),
                    'assessment' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::ASSESSMENT)->count(),
                    'preparation' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::PREPARATION)->count(),
                    'sortir' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::SORTIR)->count(),
                    'production' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::PRODUCTION)->count(),
                    'qc' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::QC)->count(),
                    // Finish: Selesai but NOT Taken (still in shop)
                    'finish' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::SELESAI)
                                         ->whereNull('taken_date')
                                         ->count(),
                ];
                $view->with('sidebarCounts', $counts);
            }
        });
    }
}
