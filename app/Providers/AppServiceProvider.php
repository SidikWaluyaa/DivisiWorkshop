<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

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
        // Cegah N+1 Lazy Loading pada environment non-production (Strict Mode)
        Model::preventLazyLoading(! app()->isProduction());

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

        // Sidebar Badges View Composer (Dioptimasi dengan once() memoization & konsolidasi kueri status)
        \Illuminate\Support\Facades\View::composer('layouts.partials.sidebar-content', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $counts = once(function () {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    $isCsOnly = !in_array($user->role, ['admin', 'owner']);

                    // Agregasi kueri status WorkOrder (termasuk finish) dalam 1 kali kueri efisien
                    $woRow = \App\Models\WorkOrder::selectRaw("
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as reception,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as assessment,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as preparation,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as sortir,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as production,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as qc,
                        SUM(CASE WHEN status = 'WAITING_PAYMENT' THEN 1 ELSE 0 END) as waiting_payment,
                        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as batal,
                        SUM(CASE WHEN status = ? AND taken_date IS NULL THEN 1 ELSE 0 END) as finish
                    ", [
                        \App\Enums\WorkOrderStatus::SPK_PENDING->value,
                        \App\Enums\WorkOrderStatus::ASSESSMENT->value,
                        \App\Enums\WorkOrderStatus::PREPARATION->value,
                        \App\Enums\WorkOrderStatus::SORTIR->value,
                        \App\Enums\WorkOrderStatus::PRODUCTION->value,
                        \App\Enums\WorkOrderStatus::QC->value,
                        \App\Enums\WorkOrderStatus::BATAL->value,
                        \App\Enums\WorkOrderStatus::SELESAI->value,
                    ])->first();

                    // Agregasi kueri status CsLead dalam 1 kali kueri efisien
                    $csFilterSql = $isCsOnly ? ' AND cs_id = ' . (int) $user->id : '';
                    $csStats = \App\Models\CsLead::selectRaw("
                        SUM(CASE WHEN status = 'GREETING' AND cs_id IS NULL THEN 1 ELSE 0 END) as cs_greeting,
                        SUM(CASE WHEN status = 'KONSULTASI'{$csFilterSql} THEN 1 ELSE 0 END) as cs_konsultasi,
                        SUM(CASE WHEN status = 'FOLLOW_UP'{$csFilterSql} THEN 1 ELSE 0 END) as cs_follow_up,
                        SUM(CASE WHEN status = 'CLOSING'{$csFilterSql} THEN 1 ELSE 0 END) as cs_closing
                    ")->whereIn('status', ['GREETING', 'KONSULTASI', 'FOLLOW_UP', 'CLOSING'])->first();

                    // Agregasi kueri WorkshopManifest dalam 1 kali kueri efisien
                    $manifestRow = \App\Models\WorkshopManifest::selectRaw("
                        SUM(CASE WHEN manifest_number LIKE 'MNF-OUT-%' AND status = 'SENT' THEN 1 ELSE 0 END) as pending_outbound,
                        SUM(CASE WHEN manifest_number NOT LIKE 'MNF-OUT-%' AND status IN ('DRAFT', 'SENT') THEN 1 ELSE 0 END) as manifest_count
                    ")->first();

                    return [
                        'cs_greeting' => (int) ($csStats->cs_greeting ?? 0),
                        'cs_konsultasi' => (int) ($csStats->cs_konsultasi ?? 0),
                        'cs_follow_up' => (int) ($csStats->cs_follow_up ?? 0),
                        'cs_closing' => (int) ($csStats->cs_closing ?? 0),
                        'reception' => (int) ($woRow->reception ?? 0),
                        'assessment' => (int) ($woRow->assessment ?? 0),
                        'preparation' => (int) ($woRow->preparation ?? 0),
                        'sortir' => (int) ($woRow->sortir ?? 0),
                        'production' => (int) ($woRow->production ?? 0),
                        'qc' => (int) ($woRow->qc ?? 0),
                        'waiting_payment' => (int) ($woRow->waiting_payment ?? 0),
                        'batal' => (int) ($woRow->batal ?? 0),
                        // Finish: Selesai but NOT Taken (still in shop)
                        'finish' => (int) ($woRow->finish ?? 0),
                        'follow_up_closing' => \App\Models\CxIssue::where('source', 'GUDANG')->where('status', 'OPEN')->count(),
                        'pending_outbound' => (int) ($manifestRow->pending_outbound ?? 0),
                        'total_stored' => \App\Models\StorageAssignment::stored()->count(),
                        'manifest_count' => (int) ($manifestRow->manifest_count ?? 0),
                        'pending_cs_payment' => \App\Models\OrderPayment::where('is_verified', false)->where('notes', 'LIKE', '%dari CS%')->count(),
                        'unverified_payment' => \App\Models\InvoicePayment::where('verified', false)->count(),
                        'oto_pending' => \App\Models\OTO::where('status', 'PENDING_CX')->count(),
                        'pending_claims' => \App\Models\WarrantyClaim::where('status', 'PENDING')->count(),
                        'verified_today' => \App\Models\Customer::where('is_address_verified', 1)->whereDate('address_verified_at', \Carbon\Carbon::today())->count(),
                    ];
                });
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
            $allServices = once(fn () => \App\Models\Service::orderBy('name')->get());
            $view->with('allServices', $allServices);
        });
    }
}
