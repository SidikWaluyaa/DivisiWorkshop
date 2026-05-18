<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    // Tabs: active, history, cancelled
    public $currentTab = 'active';

    // Filters
    public $search = '';
    public $sort = 'desc'; 
    public $start_date = '';
    public $end_date = '';
    public $handler_id = '';
    public $last_status = '';
    public $source = '';
    public $category = ''; 

    // Modal Action State
    public $showActionModal = false;
    public $actionType = ''; 
    public $selectedOrderId = null;
    public $selectedOrder = null;
    public $actionNotes = '';
    public $newEstimationDate = '';

    // Tambah Jasa State
    public $addedServices = [];
    public $selectedCategory = '';
    public $serviceSearch = '';
    public $selectedServiceId = null;
    public $isCustomService = false;
    public $customServiceName = '';
    public $servicePrice = 0;
    public $serviceDetails = '';

    protected $queryString = [
        'currentTab' => ['except' => 'active', 'as' => 't'],
        'search' => ['except' => ''],
        'sort' => ['except' => 'desc'],
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
    ];

    public function switchTab($tab)
    {
        $this->currentTab = $tab;
        $this->resetFilters();
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'start_date', 'end_date', 'handler_id', 'last_status', 'source', 'category']);
        $this->sort = ($this->currentTab === 'active') ? 'asc' : 'desc';
    }

    public function openEditModal($id)
    {
        $this->dispatch('open-edit-issue-modal', id: $id)->to(EditIssueModal::class);
    }

    public function openActionModal($orderId, $type)
    {
        $this->selectedOrderId = $orderId;
        $this->selectedOrder = WorkOrder::with('cxIssues')->find($orderId);
        $this->actionType = $type;
        $this->actionNotes = '';
        $this->newEstimationDate = '';
        $this->addedServices = [];
        $this->showActionModal = true;

        if ($type === 'komplain') {
            return redirect()->route('admin.complaints.create', ['spk' => $this->selectedOrder->spk_number]);
        }
    }

    public function closeActionModal()
    {
        $this->showActionModal = false;
        $this->reset(['selectedOrderId', 'selectedOrder', 'actionType', 'actionNotes', 'newEstimationDate', 'addedServices']);
    }

    public function toggleShippingStatus($issueId)
    {
        $issue = CxIssue::find($issueId);
        if ($issue) {
            $newStatus = $issue->shipping_status === 'SEND' ? 'HOLD' : 'SEND';
            $issue->update(['shipping_status' => $newStatus]);
            session()->flash('success', "Status pengiriman SPK #{$issue->spk_number} diubah.");
        }
    }

    public function restoreFromCancel($orderId)
    {
        DB::transaction(function () use ($orderId) {
            $order = WorkOrder::findOrFail($orderId);
            $order->update(['status' => WorkOrderStatus::CX_FOLLOWUP->value]);
            
            $lastIssue = $order->cxIssues()->where('status', 'RESOLVED')->latest()->first();
            if ($lastIssue) {
                $lastIssue->update([
                    'status' => 'OPEN',
                    'resolved_by' => null,
                    'resolved_at' => null
                ]);
            }

            $order->logs()->create([
                'step' => 'CX_FOLLOWUP',
                'action' => 'CX_RESTORE_CANCEL',
                'user_id' => Auth::id(),
                'description' => "Order dikembalikan dari status BATAL ke CX Follow Up"
            ]);
        });
        session()->flash('success', "Order dikembalikan ke daftar kerja CX.");
    }

    public function updatedSelectedServiceId($value)
    {
        if ($value && $value !== 'custom') {
            $service = Service::find($value);
            if ($service) $this->servicePrice = $service->price;
        } else {
            $this->servicePrice = 0;
        }
    }

    public function processAction()
    {
        return DB::transaction(function () {
            $order = WorkOrder::findOrFail($this->selectedOrderId);
            $issue = $order->cxIssues()->where('status', 'OPEN')->latest()->first();
            $message = match ($this->actionType) {
                'lanjut' => $this->handleLanjutAction($order),
                'cancel' => $this->handleCancelAction($order),
                'tambah_jasa' => $this->handleTambahJasaAction($order, $issue),
                default => 'Aksi tidak dikenal'
            };

            $this->finalizeProcess($order, $issue, Auth::user(), $message);
            $this->closeActionModal();
            session()->flash('success', $message);
        });
    }

    protected function handleLanjutAction($order)
    {
        $previousStatus = $order->previous_status;
        $nextStatus = ($order->cxIssues()->where('category', 'OVERLOAD')->exists()) 
            ? WorkOrderStatus::ASSESSMENT 
            : (($previousStatus && $previousStatus !== WorkOrderStatus::CX_FOLLOWUP) ? $previousStatus : WorkOrderStatus::PRODUCTION);

        $order->update([
            'status' => $nextStatus,
            'previous_status' => WorkOrderStatus::CX_FOLLOWUP,
            'estimation_date' => $this->newEstimationDate ?: $order->estimation_date,
        ]);

        if ($this->actionNotes) {
            $order->technician_notes = trim($order->technician_notes . "\n\n[CX]: " . $this->actionNotes);
            $order->save();
        }

        return "Order dilanjutkan kembali ke proses sebelumnya.";
    }

    protected function handleCancelAction($order)
    {
        $order->update(['status' => WorkOrderStatus::BATAL->value]);
        return "Order #{$order->spk_number} dibatalkan.";
    }

    protected function handleTambahJasaAction($order, $issue)
    {
        foreach ($this->addedServices as $service) {
            $order->workOrderServices()->create([
                'service_id' => $service['service_id'],
                'custom_service_name' => $service['custom_name'],
                'category_name' => $service['category_name'],
                'cost' => $service['cost'],
                'service_details' => [], // Kosongkan atau biarkan default
                'status' => 'pending',
                'notes' => $service['details'] ?: 'Tambah Jasa ' . $service['display_name'] // Ini yang akan jadi "NB" di Print SPK
            ]);
        }
        
        $isFromWorkshop = $issue && str_starts_with($issue->source, 'WORKSHOP_');

        if (!$isFromWorkshop) {
            $targetStatus = WorkOrderStatus::WAITING_PAYMENT;
            $order->update([
                'status' => $targetStatus,
                'previous_status' => null
            ]);
            $message = "Layanan tambahan diinput. Karena barang di luar Workshop, SPK dialihkan ke Waiting Payment.";
        } else {
            $targetStatus = ($order->previous_status && $order->previous_status !== WorkOrderStatus::CX_FOLLOWUP) 
                ? $order->previous_status 
                : WorkOrderStatus::PRODUCTION;
            
            $order->update([
                'status' => $targetStatus, 
                'previous_status' => WorkOrderStatus::CX_FOLLOWUP
            ]);
            $message = "Layanan tambahan berhasil diinput dan order kembali ke status " . str_replace('_', ' ', $targetStatus->value) . ".";
        }
        
        $order->recalculateTotalPrice();
        return $message;
    }

    protected function finalizeProcess($order, $issue, $user, $message)
    {
        $payload = [
            'status' => 'RESOLVED',
            'resolved_by' => $user->id,
            'resolved_at' => now(),
            'resolution_notes' => $this->actionNotes,
            'resolution_type' => $this->actionType
        ];
        if ($issue) $issue->update($payload);
        CxIssue::where('work_order_id', $order->id)->where('status', 'OPEN')->update($payload);
    }

    public function addServiceToList()
    {
        if ($this->selectedServiceId === 'custom') {
            if (!$this->customServiceName) return;
            $name = $this->customServiceName;
            $serviceId = null;
        } else {
            if (!$this->selectedServiceId) return;
            $service = Service::find($this->selectedServiceId);
            $name = $service->name;
            $serviceId = $this->selectedServiceId;
        }

        $this->addedServices[] = [
            'id' => microtime(true),
            'service_id' => $serviceId,
            'category_name' => $this->selectedCategory ?: 'Custom',
            'custom_name' => ($this->selectedServiceId === 'custom') ? $this->customServiceName : null,
            'display_name' => $name,
            'cost' => (int)$this->servicePrice,
            'details' => $this->serviceDetails,
            'is_custom' => ($this->selectedServiceId === 'custom')
        ];
        $this->reset(['selectedServiceId', 'customServiceName', 'servicePrice', 'serviceDetails']);
    }

    public function removeService($id)
    {
        $this->addedServices = array_filter($this->addedServices, fn($s) => $s['id'] !== $id);
    }

    public function render()
    {
        $user = Auth::user();
        
        // Count for Active tab always visible
        $activeCount = WorkOrder::whereIn('status', [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value]);
        if (!in_array($user->role, ['admin', 'owner'])) $activeCount->where('cx_handler_id', $user->id);
        $activeCount = $activeCount->count();

        if ($this->currentTab === 'history') {
            $query = CxIssue::where('status', 'RESOLVED')
                ->with(['workOrder', 'resolver', 'reporter']);
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('spk_number', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                      ->orWhere('category', 'like', '%' . $this->search . '%');
                });
            }
            if ($this->category) $query->where('category', $this->category);
            if ($this->start_date) $query->whereDate('resolved_at', '>=', $this->start_date);
            if ($this->end_date) $query->whereDate('resolved_at', '<=', $this->end_date);
            $data = $query->orderBy('resolved_at', $this->sort)->paginate(15);
        } 
        elseif ($this->currentTab === 'cancelled') {
            $query = WorkOrder::where('status', WorkOrderStatus::BATAL->value)
                ->with(['logs', 'cxIssues']);
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('spk_number', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            }
            if ($this->start_date) $query->whereDate('updated_at', '>=', $this->start_date);
            if ($this->end_date) $query->whereDate('updated_at', '<=', $this->end_date);
            $data = $query->orderBy('updated_at', $this->sort)->paginate(15);
        }
        else {
            $query = WorkOrder::whereIn('status', [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value])
                ->with(['cxIssues' => fn($q) => $q->latest(), 'cxHandler']);
            if (!in_array($user->role, ['admin', 'owner'])) $query->where('cx_handler_id', $user->id);
            if ($this->handler_id && in_array($user->role, ['admin', 'owner'])) $query->where('cx_handler_id', $this->handler_id);
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('spk_number', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            }
            if ($this->last_status) {
                if ($this->last_status === 'QC_REJECT') $query->whereNull('previous_status');
                else $query->where('previous_status', $this->last_status);
            }
            if ($this->source) {
                $query->whereHas('cxIssues', function($q) {
                    $q->where('source', $this->source === 'WS' ? 'LIKE' : '=', $this->source === 'WS' ? 'WORKSHOP_%' : $this->source)->where('status', 'OPEN');
                });
            }
            $data = $query->orderBy('entry_date', $this->sort)->paginate(10);
        }

        $categories = CxIssue::select('category')->whereNotNull('category')->distinct()->pluck('category');
        $masterServices = Service::all();
        $masterCategories = Service::select('category')->distinct()->pluck('category');

        return view('livewire.cx.index', [
            'data' => $data,
            'activeCount' => $activeCount, // Pass specific count
            'categories' => $categories,
            'masterServices' => $masterServices,
            'masterCategories' => $masterCategories
        ]);
    }
}
