<?php

namespace App\Livewire\Mobile;

use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Traits\HasStationTracking;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

#[Layout('layouts.mobile-pwa')]
#[Title('Pusat Kontrol Produksi — Mobile Workshop')]
class ProductionControlling extends Component
{
    use WithPagination;
    use HasStationTracking;

    /** 
     * Stasiun murni Produksi Reparasi (Stasiun 4).
     * Catatan: Treatment / Cleaning masuk ranah Quality Control (Stasiun 6).
     */
    public const STATIONS = ['prod_sol', 'prod_upper', 'qc_jahit'];

    #[Url]
    public $activeTab = 'queued'; // 'running', 'queued', 'ready_qc'

    #[Url]
    public $stationFilter = 'all'; // 'all', 'prod_sol', 'prod_upper', 'qc_jahit'

    #[Url]
    public $search = '';

    // Modal konfirmasi selesai
    public $confirmOrderId = null;
    public $confirmStation = null;
    public $confirmDuration = null;

    // Preset alasan jeda pengerjaan
    public const PAUSE_REASONS = [
        'Istirahat / Makan / Sholat',
        'Menunggu Bahan / Material / Sparepart',
        'Menunggu Lem Kering / Proses Kimia',
        'Kerjakan SPK Prioritas Lain',
        'Kendala Mesin / Alat Workshop',
    ];

    // Bottom sheet jeda pengerjaan
    public $pauseOrderId = null;
    public $pauseStation = null;
    public $pauseReason = 'Istirahat / Makan / Sholat';
    public $pauseCustomNote = '';

    // Bottom sheet mulai stasiun (pilih teknisi / tidak diperlukan)
    public $sheetOrderId = null;
    public $sheetStation = null;
    public $sheetTechId = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStationFilter() { $this->resetPage(); }
    public function updatingActiveTab() { $this->resetPage(); }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->stationFilter = 'all';
        $this->resetPage();
    }

    public function setStationFilter($filter)
    {
        $this->stationFilter = $filter;
        $this->resetPage();
    }

    public function getTechsProperty()
    {
        $excludedSpecs = ['PIC Material Sol', 'PIC Material Upper', 'PIC Material', 'PIC MATERIAL SOL', 'PIC MATERIAL UPPER'];

        $allTechs = User::where('is_active', true)
            ->where('role', 'technician')
            ->whereNotIn('specialization', $excludedSpecs)
            ->where('name', 'not like', '%Dr. Shoe%')
            ->select('id', 'name', 'station', 'specialization')
            ->orderBy('name', 'asc')
            ->get();

        $notMaterial = fn($u) => !str_contains(strtolower($u->specialization ?? ''), 'material');

        return [
            'prod_sol' => $allTechs->filter(fn($u) => $u->station === 'SOLING' && $notMaterial($u))->values(),
            'prod_upper' => $allTechs->filter(fn($u) => ($u->station === 'UPPER' || str_contains(strtolower($u->specialization ?? ''), 'upper')) && $notMaterial($u))->values(),
            'qc_jahit' => $allTechs->filter(fn($u) => str_contains(strtolower($u->specialization ?? ''), 'jahit'))->values(),
            'all' => $allTechs,
        ];
    }

    /* ------------------------------------------------------------------
     |  Bottom sheet: mulai stasiun dengan pilihan teknisi
     * -----------------------------------------------------------------*/

    public function openStartSheet($orderId, $station)
    {
        $order = WorkOrder::find($orderId);
        if (!$order || !in_array($station, self::STATIONS, true)) return;

        $this->sheetOrderId = $order->id;
        $this->sheetStation = $station;
        $this->sheetTechId = $this->defaultTechFor($order, $station);
    }

    public function setSheetStation($station)
    {
        $order = $this->sheetOrderId ? WorkOrder::find($this->sheetOrderId) : null;
        if (!$order || !in_array($station, self::STATIONS, true)) return;

        $this->sheetStation = $station;
        $this->sheetTechId = $this->defaultTechFor($order, $station);
    }

    public function selectSheetTech($techId)
    {
        $this->sheetTechId = (string) $techId;
    }

    public function closeSheet()
    {
        $this->sheetOrderId = null;
        $this->sheetStation = null;
        $this->sheetTechId = '';
    }

    public function confirmStartSheet()
    {
        if (!$this->sheetOrderId || !$this->sheetStation) return;

        $orderId = $this->sheetOrderId;
        $station = $this->sheetStation;
        $techId = (string) $this->sheetTechId;

        if ($techId === '') {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih teknisi atau "Tidak Diperlukan" dahulu');
            return;
        }

        $this->closeSheet();

        if ($techId === 'unneeded') {
            $this->markUnneededInline($orderId, $station);
            return;
        }

        $this->startStationInline($orderId, $station, (int) $techId);
    }

    private function defaultTechFor(WorkOrder $order, string $station): string
    {
        if ($order->{"{$station}_by"}) {
            return (string) $order->{"{$station}_by"};
        }

        $isStationOrdered = match($station) {
            'prod_sol' => (bool)$order->needs_prod_sol,
            'prod_upper' => (bool)$order->needs_prod_upper,
            'qc_jahit' => (bool)$order->needs_prod_jahit,
            default => true,
        };

        if (!$isStationOrdered || $order->isStationUnneeded($station)) {
            return 'unneeded';
        }

        return Auth::user()?->role === 'technician' ? (string) Auth::id() : '';
    }

    /* ------------------------------------------------------------------
     |  Aksi stasiun
     * -----------------------------------------------------------------*/

    public function startStationInline($orderId, $station, $techId = null)
    {
        $order = WorkOrder::find($orderId);
        if (!$order) return;

        $assignedTechId = $techId ?: ($order->{"{$station}_by"} ?: (Auth::user()->role === 'technician' ? Auth::id() : null));

        if (!$assignedTechId) {
            $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih teknisi terlebih dahulu');
            return;
        }

        try {
            if ($order->isStationUnneeded($station)) {
                $order->restoreStationNeeded($station, (int) $assignedTechId, Auth::id());
                $order->refresh();
            }

            $this->handleStationUpdate(
                $order,
                $station,
                'start',
                Auth::id(),
                $assignedTechId,
                WorkOrderStatus::PRODUCTION->value
            );
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Pengerjaan stasiun dimulai!');
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function promptFinishInline($orderId, $station)
    {
        $order = WorkOrder::find($orderId);
        if (!$order) return;

        $pauseInfo = $this->getStationPauseInfo($order, $station);
        $netSeconds = $pauseInfo['net_elapsed_seconds'];
        $diffMinutes = max(1, (int) round($netSeconds / 60));

        $durationText = $diffMinutes > 60
            ? floor($diffMinutes / 60) . ' Jam ' . ($diffMinutes % 60) . ' Menit'
            : $diffMinutes . ' Menit';

        if ($pauseInfo['total_paused_seconds'] > 0) {
            $pausedMins = max(1, (int) round($pauseInfo['total_paused_seconds'] / 60));
            $durationText .= " (Bersih • Total Jeda {$pausedMins} Menit)";
        }

        $this->confirmOrderId = $orderId;
        $this->confirmStation = $station;
        $this->confirmDuration = $durationText;
    }

    /* ------------------------------------------------------------------
     |  Jeda Pengerjaan (Pause / Resume) Stasiun
     * -----------------------------------------------------------------*/

    public function openPauseSheet($orderId, $station)
    {
        $this->pauseOrderId = (int) $orderId;
        $this->pauseStation = $station;
        $this->pauseReason = self::PAUSE_REASONS[0];
        $this->pauseCustomNote = '';
    }

    public function selectPauseReason($reason)
    {
        $this->pauseReason = $reason;
    }

    public function closePauseSheet()
    {
        $this->pauseOrderId = null;
        $this->pauseStation = null;
        $this->pauseCustomNote = '';
    }

    public function confirmPause()
    {
        if (!$this->pauseOrderId || !$this->pauseStation) return;

        $order = WorkOrder::find($this->pauseOrderId);
        if (!$order) {
            $this->closePauseSheet();
            return;
        }

        try {
            $this->pauseStationTracking(
                $order,
                $this->pauseStation,
                $this->pauseReason,
                Auth::id(),
                WorkOrderStatus::PRODUCTION->value,
                $this->pauseCustomNote
            );
            $this->closePauseSheet();
            $this->dispatch('swal:toast', icon: 'info', title: 'Pengerjaan berhasil dijeda');
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function resumeStationInline($orderId, $station)
    {
        $order = WorkOrder::find($orderId);
        if (!$order) return;

        try {
            $this->resumeStationTracking(
                $order,
                $station,
                Auth::id(),
                WorkOrderStatus::PRODUCTION->value
            );
            $this->dispatch('swal:toast', icon: 'success', title: 'Pengerjaan dilanjutkan!');
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function cancelConfirm()
    {
        $this->confirmOrderId = null;
        $this->confirmStation = null;
        $this->confirmDuration = null;
    }

    public function executeFinishInline()
    {
        if (!$this->confirmOrderId || !$this->confirmStation) return;

        $order = WorkOrder::find($this->confirmOrderId);
        $station = $this->confirmStation;

        $this->cancelConfirm();

        if (!$order) return;

        try {
            $this->handleStationUpdate(
                $order,
                $station,
                'finish',
                Auth::id(),
                null,
                WorkOrderStatus::PRODUCTION->value
            );
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Stasiun berhasil diselesaikan!');
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function markUnneededInline($orderId, $station)
    {
        $order = WorkOrder::find($orderId);
        if (!$order) return;

        $order->markStationUnneeded($station, Auth::id());
        $this->dispatch('swal:toast', icon: 'info', title: 'Stasiun ditandai Tidak Diperlukan');
    }

    /* ------------------------------------------------------------------
     |  Query scopes (SINKRON 100% DENGAN DESKTOP PRODUCTION STATIONINDEX)
     * -----------------------------------------------------------------*/

    /**
     * Base query produksi: Status PRODUCTION, bukan R&D, bukan OTO
     */
    private function baseProductionQuery(): Builder
    {
        return WorkOrder::withoutRnd()
            ->where('status', WorkOrderStatus::PRODUCTION->value)
            ->whereDoesntHave('otos', fn($q) => $q->whereIn('status', ['ACCEPTED', 'IN_PROGRESS']));
    }

    /**
     * Antrean Kerja Reparasi (Fisik belum selesai & belum di-ACC Admin)
     * Sama persis dengan $reparasiQuery di StationIndex.php desktop
     */
    private function activeReparasiQuery(): Builder
    {
        return $this->baseProductionQuery()->where(function ($q) {
            $q->whereDoesntHave('workOrderServices')
              ->orWhereHas('workOrderServices', function ($sq) {
                  $sq->where(function ($ssq) {
                      $ssq->where('category_name', 'like', '%Upper%')
                          ->whereNull('work_orders.prod_upper_completed_at')
                          ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"prod_upper"%'));
                  })
                  ->orWhere(function ($ssq) {
                      $ssq->where('category_name', 'like', '%Sol%')
                          ->whereNull('work_orders.prod_sol_completed_at')
                          ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"prod_sol"%'));
                  })
                  ->orWhere(function ($ssq) {
                      $ssq->where(function ($x) { $x->where('category_name', 'like', '%Sol%')->orWhere('category_name', 'like', '%Upper%')->orWhere('category_name', 'like', '%Jahit%'); })
                          ->whereNull('work_orders.qc_jahit_completed_at')
                          ->where(fn($uq) => $uq->whereNull('unneeded_stations')->orWhere('unneeded_stations', 'not like', '%"qc_jahit"%'));
                  });
              });
        })->whereDoesntHave('logs', fn($lq) => $lq->where('step', 'PRODUCTION')->where('action', 'PRODUCTION_APPROVED'))
          ->whereDoesntHave('suratJalanItems.suratJalan', fn($q) => $q->where('jenis_serah_terima', 'produksi_to_post_qc'));
    }

    /**
     * SPK Selesai Hari Ini (Salah satu atau seluruh stasiun Sol/Upper/Jahit selesai hari ini)
     */
    private function completedTodayQuery(): Builder
    {
        return WorkOrder::withoutRnd()
            ->where(function ($q) {
                $q->whereDate('prod_sol_completed_at', today())
                  ->orWhereDate('prod_upper_completed_at', today())
                  ->orWhereDate('qc_jahit_completed_at', today());
            });
    }

    /**
     * Tab eksklusif:
     * - 'running': Stasiun Sol/Upper/Jahit sedang berjalan
     * - 'queued': Belum ada stasiun Sol/Upper/Jahit yang berjalan (masih antre pengerjaan fisik)
     * - 'completed_today': Salah satu / seluruh stasiun fisik selesai hari ini
     */
    private function applyTab(Builder $q, string $tab): Builder
    {
        if ($tab === 'running') {
            return $q->where(function($sq) {
                $sq->whereNotNull('prod_upper_started_at')->whereNull('prod_upper_completed_at')
                   ->orWhere(fn($x) => $x->whereNotNull('prod_sol_started_at')->whereNull('prod_sol_completed_at'))
                   ->orWhere(fn($x) => $x->whereNotNull('qc_jahit_started_at')->whereNull('qc_jahit_completed_at'));
            });
        }

        if ($tab === 'queued') {
            return $q->where(function($sq) {
                $sq->where(fn($x) => $x->whereNull('prod_upper_started_at')->orWhereNotNull('prod_upper_completed_at'))
                   ->where(fn($x) => $x->whereNull('prod_sol_started_at')->orWhereNotNull('prod_sol_completed_at'))
                   ->where(fn($x) => $x->whereNull('qc_jahit_started_at')->orWhereNotNull('qc_jahit_completed_at'));
            });
        }

        return $q;
    }

    private function applyStationFilter(Builder $q, string $tab, string $st): Builder
    {
        if ($st === 'all' || !in_array($st, self::STATIONS, true)) return $q;

        if ($tab === 'running') {
            return $q->whereNotNull("{$st}_started_at")->whereNull("{$st}_completed_at");
        }

        if ($tab === 'queued') {
            return $q->whereNull("{$st}_started_at")->whereNull("{$st}_completed_at");
        }

        if ($tab === 'completed_today') {
            return $q->whereDate("{$st}_completed_at", today());
        }

        return $q;
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $isSearching = !empty(trim($this->search));

        if ($isSearching) {
            // Universal Search Mode (Sinkron dengan Desktop StationIndex.php)
            // Mencari ke seluruh SPK Produksi aktif tanpa terhalang tab atau filter sub-stasiun
            $rawTerm = trim($this->search);
            $normalizedTerm = str_replace(["\xe2\x80\x93", "\xe2\x80\x94", '–', '—'], '-', $rawTerm);
            $terms = array_unique(array_filter([$rawTerm, $normalizedTerm]));

            $baseQuery = $this->baseProductionQuery()->with(['customer', 'photos', 'workOrderServices', 'logs']);

            $baseQuery->where(function ($q) use ($terms) {
                foreach ($terms as $t) {
                    $likeTerm = '%' . $t . '%';
                    $q->orWhere('spk_number', 'like', $likeTerm)
                      ->orWhere('customer_name', 'like', $likeTerm)
                      ->orWhere('customer_phone', 'like', $likeTerm)
                      ->orWhere('shoe_brand', 'like', $likeTerm)
                      ->orWhere('shoe_type', 'like', $likeTerm)
                      ->orWhereHas('customer', function ($cq) use ($likeTerm) {
                          $cq->where('name', 'like', $likeTerm)
                             ->orWhere('phone', 'like', $likeTerm);
                      });
                }
            });
        } elseif ($this->activeTab === 'completed_today') {
            $baseQuery = $this->completedTodayQuery()->with(['customer', 'photos', 'workOrderServices', 'logs']);
            $this->applyStationFilter($baseQuery, $this->activeTab, $this->stationFilter);
        } else {
            $baseQuery = $this->activeReparasiQuery()->with(['customer', 'photos', 'workOrderServices', 'logs']);
            $this->applyTab($baseQuery, $this->activeTab);
            $this->applyStationFilter($baseQuery, $this->activeTab, $this->stationFilter);
        }

        $orders = $baseQuery->orderBy('updated_at', 'desc')->paginate(15);

        // Hitungan tab
        $runningCount = $this->applyTab($this->activeReparasiQuery(), 'running')->count();
        $queuedCount = $this->applyTab($this->activeReparasiQuery(), 'queued')->count();
        $completedTodayCount = $this->completedTodayQuery()->count();

        $counts = [
            'running' => $runningCount,
            'queued' => $queuedCount,
            'completed_today' => $completedTodayCount,
        ];

        // Hitungan per stasiun untuk tab aktif
        $stationCounts = ['all' => $counts[$this->activeTab] ?? 0];
        if (in_array($this->activeTab, ['running', 'queued'], true)) {
            foreach (self::STATIONS as $st) {
                $q = $this->applyTab($this->activeReparasiQuery(), $this->activeTab);
                $stationCounts[$st] = $this->applyStationFilter($q, $this->activeTab, $st)->count();
            }
        } elseif ($this->activeTab === 'completed_today') {
            foreach (self::STATIONS as $st) {
                $q = $this->completedTodayQuery();
                $stationCounts[$st] = $this->applyStationFilter($q, $this->activeTab, $st)->count();
            }
        }

        $sheetOrder = $this->sheetOrderId ? WorkOrder::with(['customer', 'workOrderServices'])->find($this->sheetOrderId) : null;

        return view('livewire.mobile.production-controlling', [
            'orders' => $orders,
            'counts' => $counts,
            'stationCounts' => $stationCounts,
            'techOptions' => ($sheetOrder ? $this->techs : []),
            'sheetOrder' => $sheetOrder,
            'activeTab' => $this->activeTab,
            'stationFilter' => $this->stationFilter,
            'search' => $this->search,
            'confirmOrderId' => $this->confirmOrderId,
            'confirmStation' => $this->confirmStation,
            'confirmDuration' => $this->confirmDuration,
            'pauseOrderId' => $this->pauseOrderId,
            'pauseStation' => $this->pauseStation,
            'pauseReason' => $this->pauseReason,
            'pauseCustomNote' => $this->pauseCustomNote,
        ])->layout('layouts.mobile-pwa');
    }
}
