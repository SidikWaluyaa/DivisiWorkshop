<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CxAfterConfirmation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AfterConfirmationList extends Component
{
    use WithPagination;

    public $search = '';
    public $response = '';
    public $startDate = '';
    public $endDate = '';
    public $stageFilter = '';
    public $roundFilter = '';
    public $pickupFilter = '';

    // Modal state
    public $editingId = null;
    public $editingResponse = '';
    public $editingNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'response' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'stageFilter' => ['except' => ''],
        'roundFilter' => ['except' => ''],
        'pickupFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingResponse()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function updatingStageFilter()
    {
        $this->resetPage();
    }

    public function updatingRoundFilter()
    {
        $this->resetPage();
    }

    public function updatingPickupFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'response', 'startDate', 'endDate', 'stageFilter', 'roundFilter', 'pickupFilter']);
        $this->resetPage();
    }

    public function setStageQuickFilter($stage = '')
    {
        $this->stageFilter = $stage;
        $this->roundFilter = '';
        $this->resetPage();
    }

    public function setRoundQuickFilter($round = '')
    {
        $this->roundFilter = $round;
        $this->stageFilter = '';
        $this->resetPage();
    }

    public function edit($id)
    {
        $item = CxAfterConfirmation::findOrFail($id);
        $this->editingId = $id;
        $this->editingResponse = $item->response;
        $this->editingNotes = $item->notes;

        $this->dispatch('open-edit-modal');
    }

    public function save()
    {
        $this->validate([
            'editingResponse' => 'nullable|in:Puas,Komplain,Kurang Puas,No Respon 1x24 Jam,Hold',
            'editingNotes' => 'nullable|string',
        ]);

        $item = CxAfterConfirmation::findOrFail($this->editingId);
        
        $data = [
            'response' => $this->editingResponse,
            'notes' => $this->editingNotes,
        ];

        // Auto-set PIC and Contact Date if response is being set for the first time
        if (!empty($this->editingResponse) && is_null($item->pic_id)) {
            $data['pic_id'] = Auth::id();
            $data['contacted_at'] = now();
        }

        $item->update($data);

        $this->editingId = null;
        $this->dispatch('close-edit-modal');
        $this->dispatch('notify', ['message' => 'Data konfirmasi berhasil diperbarui.']);
    }

    public function render()
    {
        $query = CxAfterConfirmation::with(['workOrder', 'pic'])
            ->orderBy('entered_at', 'desc');

        // Filter by Date
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('entered_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        // Filter by Response
        if ($this->response) {
            $query->where('response', $this->response);
        }

        // Filter by Stage Type (Garansi, Revisi, OTO, R&D, Reguler)
        // Menampilkan seluruh siklus pengerjaan (After 1 Reguler, After 2 OTO/Revisi/Garansi, dst.) milik SPK terkait
        if ($this->stageFilter) {
            $stage = strtoupper(trim($this->stageFilter));
            if ($stage === 'REGULER') {
                $query->where('stage_type', 'REGULER');
            } else {
                $query->whereHas('workOrder.afterConfirmations', function ($q) use ($stage) {
                    $q->where('stage_type', $stage);
                });
            }
        }

        // Filter by Round (After 1 / After 2 / After 3 / After 2+)
        if ($this->roundFilter) {
            if ($this->roundFilter === '2+') {
                $query->where('round', '>=', 2);
            } else {
                $query->where('round', (int)$this->roundFilter);
            }
        }

        // Filter by Pickup Status (Sudah Diambil / Belum Diambil)
        if ($this->pickupFilter === 'taken') {
            $query->whereHas('workOrder', function ($q) {
                $q->whereNotNull('taken_date');
            });
        } elseif ($this->pickupFilter === 'not_taken') {
            $query->whereHas('workOrder', function ($q) {
                $q->whereNull('taken_date');
            });
        }

        // Search by SPK, Customer Name, or Phone
        if ($this->search) {
            $search = $this->search;
            $query->whereHas('workOrder', function ($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Dynamic stats filtered by active date range
        $statsBase = CxAfterConfirmation::query();
        if ($this->startDate && $this->endDate) {
            $statsBase->whereBetween('entered_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        $stats = [
            'total'   => (clone $statsBase)->count(),
            'after1'  => (clone $statsBase)->where('round', 1)->count(),
            'after2'  => (clone $statsBase)->where('round', '>=', 2)->count(),
            'reguler' => (clone $statsBase)->where('stage_type', 'REGULER')->count(),
            'garansi' => (clone $statsBase)->where('stage_type', 'GARANSI')->count(),
            'revisi'  => (clone $statsBase)->where('stage_type', 'REVISI')->count(),
            'oto'     => (clone $statsBase)->where('stage_type', 'OTO')->count(),
            'rnd'     => (clone $statsBase)->where('stage_type', 'RND')->count(),
        ];

        return view('livewire.cx.after-confirmation-list', [
            'items' => $query->paginate(20),
            'stats' => $stats,
        ]);
    }
}
