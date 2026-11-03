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

                    // Agregasi kueri status WorkOrder dalam 1 kali kueri efisien
                    $woCounts = \App\Models\WorkOrder::selectRaw('status, count(*) as cnt')
                        ->whereIn('status', [
                            \App\Enums\WorkOrderStatus::SPK_PENDING->value,
                            \App\Enums\WorkOrderStatus::ASSESSMENT->value,
                            \App\Enums\WorkOrderStatus::PREPARATION->value,
                            \App\Enums\WorkOrderStatus::SORTIR->value,
                            \App\Enums\WorkOrderStatus::PRODUCTION->value,
                            \App\Enums\WorkOrderStatus::QC->value,
                            'WAITING_PAYMENT',
                            \App\Enums\WorkOrderStatus::BATAL->value,
                        ])
                        ->groupBy('status')
                        ->pluck('cnt', 'status');

                    return [
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
                        'reception' => (int) ($woCounts[\App\Enums\WorkOrderStatus::SPK_PENDING->value] ?? 0),
                        'assessment' => (int) ($woCounts[\App\Enums\WorkOrderStatus::ASSESSMENT->value] ?? 0),
                        'preparation' => (int) ($woCounts[\App\Enums\WorkOrderStatus::PREPARATION->value] ?? 0),
                        'sortir' => (int) ($woCounts[\App\Enums\WorkOrderStatus::SORTIR->value] ?? 0),
                        'production' => (int) ($woCounts[\App\Enums\WorkOrderStatus::PRODUCTION->value] ?? 0),
                        'qc' => (int) ($woCounts[\App\Enums\WorkOrderStatus::QC->value] ?? 0),
                        'waiting_payment' => (int) ($woCounts['WAITING_PAYMENT'] ?? 0),
                        'batal' => (int) ($woCounts[\App\Enums\WorkOrderStatus::BATAL->value] ?? 0),
                        // Finish: Selesai but NOT Taken (still in shop)
                        'finish' => \App\Models\WorkOrder::where('status', \App\Enums\WorkOrderStatus::SELESAI)
                                             ->whereNull('taken_date')
                                             ->count(),
                        'follow_up_closing' => \App\Models\CxIssue::where('source', 'GUDANG')->where('status', 'OPEN')->count(),
                        'pending_outbound' => \App\Models\WorkshopManifest::where('manifest_number', 'like', 'MNF-OUT-%')->where('status', 'SENT')->count(),
                        'total_stored' => \App\Models\StorageAssignment::stored()->count(),
                        'manifest_count' => \App\Models\WorkshopManifest::where('manifest_number', 'not like', 'MNF-OUT-%')->whereIn('status', ['DRAFT', 'SENT'])->count(),
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
            $view->with('allServices', \App\Models\Service::orderBy('name')->get());
        });
    }
}
