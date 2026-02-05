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
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('PDF', \Barryvdh\DomPDF\Facade\Pdf::class);

        // Messaging Service Abstraction
        $this->app->bind(\App\Contracts\MessagingService::class, function ($app) {
            $provider = env('MESSAGING_PROVIDER', 'cekat');
            
            if ($provider === 'sleekflow') {
                return new \App\Services\SleekFlowService();
            }
            
            return new \App\Services\CekatService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Register Observers
        \App\Models\WorkOrder::observe(\App\Observers\WorkOrderObserver::class);

        // Sidebar Badges View Composer
        \Illuminate\Support\Facades\View::composer('layouts.partials.sidebar-content', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $isCsOnly = !in_array($user->role, ['admin', 'owner']);

                $counts = [
                    'cs_greeting' => \App\Models\CsLead::greeting()->whereNull('cs_id')->count(),
                    'cs_konsultasi' => \App\Models\CsLead::konsultasi()
                                        ->when($isCsOnly, fn($q) => $q->where('cs_id', $user->id))
                                        ->count(),
                    'cs_closing' => \App\Models\CsLead::closing()
                                        ->when($isCsOnly, fn($q) => $q->where('cs_id', $user->id))
                                        ->count(),
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

        // Permissions & Gates
        \Illuminate\Support\Facades\Gate::define('cs.override-locked', function ($user) {
            return in_array($user->role, ['admin', 'owner']);
        });

        \Illuminate\Support\Facades\Gate::define('cs.manage-all', function ($user) {
            return in_array($user->role, ['admin', 'owner']);
        });
    }
}
