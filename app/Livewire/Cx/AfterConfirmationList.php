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

    public function resetFilters()
    {
        $this->reset(['search', 'response', 'startDate', 'endDate', 'stageFilter', 'roundFilter']);
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
        if ($this->stageFilter) {
            $query->where('stage_type', $this->stageFilter);
        }

        // Filter by Round (After 1 / After 2)
        if ($this->roundFilter) {
            $query->where('round', (int)$this->roundFilter);
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
