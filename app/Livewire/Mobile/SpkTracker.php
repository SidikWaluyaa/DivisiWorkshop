<?php

namespace App\Livewire\Mobile;

use App\Models\WorkOrder;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use App\Traits\HasStationTracking;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.mobile-pwa')]
#[Title('Detail Kontrol SPK — Mobile Workshop')]
class SpkTracker extends Component
{
    use HasStationTracking;

    public $spk_number;
    public $orderId;
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

    // State bottom sheet jeda pengerjaan
    public $pauseStation = null;
    public $pauseReason = 'Istirahat / Makan / Sholat';
    public $pauseCustomNote = '';

    public function mount($spk_number)
    {
        $this->spk_number = trim($spk_number);
        $order = WorkOrder::where('spk_number', $this->spk_number)->firstOrFail();
        $this->orderId = $order->id;
    }

    public function getOrderProperty()
    {
        return WorkOrder::with(['customer', 'workOrderServices', 'photos', 'logs.user'])
            ->findOrFail($this->orderId);
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

        return [
            'prod_sol' => $allTechs->filter(fn($u) => $u->station === 'SOLING' && !str_contains(strtolower($u->specialization ?? ''), 'material')),
            'prod_upper' => $allTechs->filter(fn($u) => ($u->station === 'UPPER' || str_contains(strtolower($u->specialization ?? ''), 'upper')) && !str_contains(strtolower($u->specialization ?? ''), 'material')),
            'qc_jahit' => $allTechs->filter(fn($u) => str_contains(strtolower($u->specialization ?? ''), 'jahit')),
            'prod_cleaning' => $allTechs->filter(fn($u) => $u->station === 'TREATMENT' || $u->station === 'CLEANING'),
            'all' => $allTechs,
        ];
    }

    public function selectTechnician($station, $techId)
    {
        $order = $this->order;

        if ($techId === 'unneeded') {
            $order->markStationUnneeded($station, Auth::id());
            $this->dispatch('swal:toast', icon: 'info', title: 'Stasiun ditandai Tidak Diperlukan');
            return;
        }

        if ($order->isStationUnneeded($station)) {
            $order->restoreStationNeeded($station, $techId ? (int)$techId : null, Auth::id());
        } else {
            $order->{"{$station}_by"} = $techId ? (int)$techId : null;
            $order->save();
        }

        $this->dispatch('swal:toast', icon: 'success', title: 'Penugasan teknisi disimpan');
    }

    public function startStation($station)
    {
        $order = $this->order;

        // Auto-assign current technician if logged in user is a technician and not assigned yet
        if (!$order->{"{$station}_by"}) {
            if (Auth::user()->role === 'technician') {
                $order->{"{$station}_by"} = Auth::id();
                $order->save();
            } else {
                $this->dispatch('swal:toast', icon: 'warning', title: 'Pilih teknisi terlebih dahulu sebelum memulai');
                return;
            }
        }

        try {
            $this->handleStationUpdate(
                $order,
                $station,
                'start',
                Auth::id(),
                $order->{"{$station}_by"},
                WorkOrderStatus::PRODUCTION->value
            );
            $order->save();
            $this->dispatch('swal:toast', icon: 'success', title: 'Pengerjaan stasiun dimulai!');
        } catch (\Throwable $e) {
            $this->dispatch('swal:toast', icon: 'error', title: $e->getMessage());
        }
    }

    public function promptFinish($station)
    {
        $order = $this->order;
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

        $this->confirmStation = $station;
        $this->confirmDuration = $durationText;
    }

    /* ------------------------------------------------------------------
     |  Jeda Pengerjaan (Pause / Resume) Stasiun
     * -----------------------------------------------------------------*/

    public function openPauseSheet($station)
    {
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
        $this->pauseStation = null;
        $this->pauseCustomNote = '';
    }

    public function confirmPause()
    {
        if (!$this->pauseStation) return;

        $order = $this->order;

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

    public function resumeStation($station)
    {
        $order = $this->order;

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
        $this->confirmStation = null;
        $this->confirmDuration = null;
    }

    public function executeFinish()
    {
        if (!$this->confirmStation) return;

        $station = $this->confirmStation;
        $this->confirmStation = null;
        $this->confirmDuration = null;

        $order = $this->order;

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

    public $forcedActiveStations = [];

    public function enableStation($station)
    {
        if ($this->order->isStationUnneeded($station)) {
            $this->order->restoreStationNeeded($station, null, Auth::id());
        }
        if (!in_array($station, $this->forcedActiveStations, true)) {
            $this->forcedActiveStations[] = $station;
        }
        $this->dispatch('swal:toast', icon: 'success', title: 'Stasiun berhasil diaktifkan');
    }

    public function render()
    {
        $order = $this->order;

        // Categorize services to know which stations are relevant (SINKRON DENGAN STANDAR DESKTOP)
        $hasSol = (bool) $order->needs_prod_sol;
        $hasUpper = (bool) $order->needs_prod_upper;
        $hasJahit = (bool) $order->needs_prod_jahit;
        $hasTreatment = (bool) $order->needs_prod_treatment;

        // Fallback jika tidak ada kategori spesifik yang terdeteksi
        if (!$hasUpper && !$hasSol && !$hasJahit) {
            $hasUpper = true;
        }

        $isUnneededStation = function(string $sKey, bool $isOrdered) use ($order) {
            if (in_array($sKey, $this->forcedActiveStations, true)) {
                return false;
            }
            if ($order->{"{$sKey}_started_at"} || $order->{"{$sKey}_by"}) {
                return false;
            }
            return !$isOrdered || $order->isStationUnneeded($sKey);
        };

        $buildStationData = function(string $key, string $name, string $category, bool $isOrdered) use ($order, $isUnneededStation) {
            $isUnneeded = $isUnneededStation($key, $isOrdered);
            $startedAt = $order->{"{$key}_started_at"};
            $completedAt = $order->{"{$key}_completed_at"};
            $isRunning = $startedAt && !$completedAt && !$isUnneeded;

            $pauseInfo = $isRunning ? $this->getStationPauseInfo($order, $key) : null;

            return [
                'name' => $name,
                'category' => $category,
                'is_ordered' => $isOrdered,
                'is_unneeded' => $isUnneeded,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'tech_id' => $order->{"{$key}_by"},
                'tech_name' => optional(User::find($order->{"{$key}_by"}))->name,
                'is_paused' => $pauseInfo['is_paused'] ?? false,
                'pause_reason' => $pauseInfo['reason'] ?? null,
                'net_seconds' => $pauseInfo['net_elapsed_seconds'] ?? 0,
                'total_paused_seconds' => $pauseInfo['total_paused_seconds'] ?? 0,
            ];
        };

        $stations = [
            'prod_sol' => $buildStationData('prod_sol', 'Stasiun Soling', 'Sol', $hasSol),
            'prod_upper' => $buildStationData('prod_upper', 'Stasiun Upper', 'Upper', $hasUpper),
            'qc_jahit' => $buildStationData('qc_jahit', 'Stasiun QC Jahit', 'Jahit', $hasJahit),
        ];

        if ($order->status === WorkOrderStatus::QC && $hasTreatment) {
            $stations['prod_cleaning'] = $buildStationData('prod_cleaning', 'Stasiun Treatment / Cleaning', 'Treatment', $hasTreatment);
        }

        return view('livewire.mobile.spk-tracker', [
            'order' => $order,
            'stations' => $stations,
            'techOptions' => $this->techs,
            'confirmStation' => $this->confirmStation,
            'confirmDuration' => $this->confirmDuration,
        ])->layout('layouts.mobile-pwa');
    }
}
