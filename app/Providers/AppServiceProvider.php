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

        // 

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Force HTTPS if request is secure or behind HTTPS proxy
        if (request()->secure() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Force Static Livewire Route to fix 404 on Laragon/Local (including web middleware)
        \Livewire\Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/livewire/update', $handle)->middleware('web');
        });

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
                    'cs_follow_up' => \App\Models\CsLead::followUp()
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

        // Module Access Gates (Standardized 7 Pillars)
        \Illuminate\Support\Facades\Gate::define('access-cs', function ($user) {
            return $user->hasAccess('cs');
        });

        \Illuminate\Support\Facades\Gate::define('access-gudang', function ($user) {
            return $user->hasAccess('gudang');
        });

        \Illuminate\Support\Facades\Gate::define('access-workshop', function ($user) {
            return $user->hasAccess('workshop') || $user->isWorkshop();
        });

        \Illuminate\Support\Facades\Gate::define('access-finance', function ($user) {
            return $user->hasAccess('finance');
        });

        \Illuminate\Support\Facades\Gate::define('access-cx', function ($user) {
            return $user->hasAccess('cx');
        });

        \Illuminate\Support\Facades\Gate::define('access-master', function ($user) {
            return $user->hasAccess('master') || $user->isAdmin() || $user->isOwner();
        });

        // Specific Governance Gates
        \Illuminate\Support\Facades\Gate::define('cs.override-locked', function ($user) {
            return $user->isAdmin() || $user->isOwner();
        });

        \Illuminate\Support\Facades\Gate::define('cs.manage-all', function ($user) {
            return $user->isAdmin() || $user->isOwner();
        });

        // View Composer for Report Modal (Suggested Services)
        \Illuminate\Support\Facades\View::composer('components.report-modal', function ($view) {
            $view->with('allServices', \App\Models\Service::orderBy('name')->get());
        });
    }
}
