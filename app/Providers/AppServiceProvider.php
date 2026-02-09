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

        // Module Access Gates (Standardized 5 Pillars)
        \Illuminate\Support\Facades\Gate::define('access-cs', function ($user) {
            return $user->hasAccess('cs') || 
                   $user->hasAccess('cs.spk') || 
                   $user->hasAccess('cs.greeting') || 
                   $user->hasAccess('cs.dashboard') || 
                   $user->hasAccess('cs.analytics');
        });

        \Illuminate\Support\Facades\Gate::define('access-gudang', function ($user) {
            return $user->hasAccess('gudang') || 
                   $user->hasAccess('warehouse.storage') || 
                   $user->hasAccess('manifest.index') || 
                   $user->hasAccess('storage.dashboard') ||
                   $user->hasAccess('material.requests');
        });

        \Illuminate\Support\Facades\Gate::define('access-workshop', function ($user) {
            return $user->hasAccess('workshop') || 
                   $user->hasAccess('workshop.dashboard') || 
                   $user->hasAccess('assessment') || 
                   $user->hasAccess('preparation') || 
                   $user->hasAccess('sortir') || 
                   $user->hasAccess('production') || 
                   $user->hasAccess('qc') || 
                   $user->hasAccess('finish') || 
                   $user->hasAccess('gallery');
        });

        \Illuminate\Support\Facades\Gate::define('access-finance', function ($user) {
            return $user->hasAccess('finance');
        });

        \Illuminate\Support\Facades\Gate::define('access-cx', function ($user) {
            return $user->hasAccess('cx') || 
                   $user->hasAccess('cx.dashboard') || 
                   $user->hasAccess('cx.oto') || 
                   $user->hasAccess('admin.complaints') ||
                   $user->hasAccess('cx.index');
        });

        // Specific Governance Gates
        \Illuminate\Support\Facades\Gate::define('cs.override-locked', function ($user) {
            return $user->isAdmin() || $user->isOwner();
        });

        \Illuminate\Support\Facades\Gate::define('cs.manage-all', function ($user) {
            return $user->isAdmin() || $user->isOwner();
        });
    }
}
