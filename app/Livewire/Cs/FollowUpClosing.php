<?php

namespace App\Livewire\Cs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FollowUpClosing extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $sort = 'desc';
    public $start_date = '';
    public $end_date = '';

    // Modal Action State
    public $showActionModal = false;
    public $actionType = ''; // 'lanjut', 'cancel', 'tambah_jasa'
    public $selectedOrderId = null;
    public $selectedOrder = null;
    public $actionNotes = '';
    public $newEstimationDate = '';

    // Tambah Jasa (Upsell) State
    public $addedServices = [];
    public $selectedCategory = '';
    public $serviceSearch = '';
    public $selectedServiceId = null;
    public $customServiceName = '';
    public $servicePrice = 0;
    public $serviceHkDays = 0;
    public $serviceDetails = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'desc'],
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
    ];

    public function updatedSelectedServiceId($value)
    {
        if ($value && $value !== 'custom') {
            $service = Service::find($value);
            if ($service) {
                $this->servicePrice = $service->price;
                $this->serviceHkDays = $service->hk_days;
            }
        } else {
            $this->servicePrice = 0;
            $this->serviceHkDays = 0;
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'start_date', 'end_date']);
        $this->sort = 'desc';
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
    }

    public function closeActionModal()
    {
        $this->showActionModal = false;
        $this->reset(['selectedOrderId', 'selectedOrder', 'actionType', 'actionNotes', 'newEstimationDate', 'addedServices', 'serviceHkDays']);
    }

    public function addServiceToList()
    {
        if ($this->selectedServiceId === 'custom') {
            if (!$this->customServiceName) {
                $this->addError('customServiceName', 'Nama jasa custom wajib diisi.');
                return;
            }
            $name = $this->customServiceName;
            $serviceId = null;
        } else {
            $service = Service::find($this->selectedServiceId);
            if (!$service) return;
            $name = $service->name;
            $serviceId = $service->id;
        }

        $this->addedServices[] = [
            'id' => microtime(true),
            'service_id' => $serviceId,
            'category_name' => $this->selectedCategory ?: 'Custom',
            'custom_name' => ($this->selectedServiceId === 'custom') ? $this->customServiceName : null,
            'display_name' => $name,
            'cost' => (int)$this->servicePrice,
            'hk_days' => (int)$this->serviceHkDays,
            'details' => $this->serviceDetails,
            'is_custom' => ($this->selectedServiceId === 'custom')
        ];
        $this->reset(['selectedServiceId', 'customServiceName', 'servicePrice', 'serviceDetails', 'serviceHkDays']);
    }

    public function removeService($id)
    {
        $this->addedServices = array_filter($this->addedServices, fn($s) => $s['id'] !== $id);
    }

    public function processAction()
    {
        return DB::transaction(function () {
            $order = WorkOrder::findOrFail($this->selectedOrderId);
            $issue = $order->cxIssues()->where('status', 'OPEN')->where('source', 'GUDANG')->latest()->first();
            
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
        // GUDANG issue resolution routes to ASSESSMENT status so it continues the physical pipeline
        $order->update([
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'previous_status' => null,
            'estimation_date' => $this->newEstimationDate ?: $order->estimation_date,
        ]);

        if ($this->actionNotes) {
            $order->technician_notes = trim($order->technician_notes . "\n\n[CS FOLLOWUP]: " . $this->actionNotes);
            $order->save();
        }

        // Add Log
        $order->logs()->create([
            'step' => 'RECEPTION',
            'action' => 'CS_FOLLOWUP_RESOLVED_CONTINUE',
            'user_id' => Auth::id(),
            'description' => 'CS Follow Up Selesai: Order dilanjutkan ke tahap Assessment. Catatan: ' . ($this->actionNotes ?: '-')
        ]);

        return "Order #{$order->spk_number} dilanjutkan kembali ke proses Assessment.";
    }

    protected function handleCancelAction($order)
    {
        $order->update(['status' => WorkOrderStatus::BATAL->value]);
        
        // Add Log
        $order->logs()->create([
            'step' => 'RECEPTION',
            'action' => 'CS_FOLLOWUP_RESOLVED_CANCEL',
            'user_id' => Auth::id(),
            'description' => 'CS Follow Up Selesai: Order dibatalkan oleh pelanggan. Alasan: ' . ($this->actionNotes ?: '-')
        ]);

        return "Order #{$order->spk_number} dibatalkan.";
    }

    protected function handleTambahJasaAction($order, $issue)
    {
        $totalNewHk = 0;
        foreach ($this->addedServices as $service) {
            $hk = (int) ($service['hk_days'] ?? 0);
            $totalNewHk += $hk;

            $order->workOrderServices()->create([
                'service_id' => $service['service_id'],
                'custom_service_name' => $service['custom_name'],
                'category_name' => $service['category_name'],
                'cost' => $service['cost'],
                'service_details' => [
                    'is_cx_additional' => true,
                    'hk_days' => $hk
                ],
                'status' => 'PENDING',
                'notes' => $service['details'] ?: 'Tambah Jasa ' . $service['display_name'],
                'created_by' => Auth::id()
            ]);
        }

        $addedServicesNames = [];
        foreach ($this->addedServices as $service) {
            $addedServicesNames[] = ($service['custom_name'] ?? $service['display_name']) . " (Rp " . number_format($service['cost'], 0, ',', '.') . ")";
        }
        $servicesDetailsStr = implode(', ', $addedServicesNames);

        // Update status
        $order->status = WorkOrderStatus::ASSESSMENT->value;
        $order->previous_status = null;
        $order->save();

        $order->recalculateTotalPrice();
        
        // Sync financial on invoice if present — recalculates estimasi_selesai natively from updated work_order_services
        if ($order->invoice) {
            $order->invoice->syncFinancials();
        }

        // Add Log
        $order->logs()->create([
            'step' => 'RECEPTION',
            'action' => 'CS_FOLLOWUP_RESOLVED_UPSELL',
            'user_id' => Auth::id(),
            'description' => 'CS Follow Up Selesai: Layanan tambahan diinput (' . $servicesDetailsStr . '). SPK dialihkan ke Assessment.'
        ]);

        return "Layanan tambahan diinput. SPK #{$order->spk_number} dialihkan ke Assessment.";
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
        CxIssue::where('work_order_id', $order->id)->where('source', 'GUDANG')->where('status', 'OPEN')->update($payload);
    }

    public function render()
    {
        $user = Auth::user();

        // Calculate count of active GUDANG issues
        $activeCount = WorkOrder::whereIn('status', [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value])
            ->whereHas('cxIssues', function ($q) {
                $q->where('source', 'GUDANG')->where('status', 'OPEN');
            })->count();

        // Fetch WorkOrders with open GUDANG issues
        $query = WorkOrder::whereIn('status', [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value])
            ->whereHas('cxIssues', function ($q) {
                $q->where('source', 'GUDANG')->where('status', 'OPEN');
            })
            ->with(['cxIssues' => fn($q) => $q->where('source', 'GUDANG')->latest(), 'cxHandler']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('spk_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->start_date) {
            $query->whereDate('updated_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('updated_at', '<=', $this->end_date);
        }

        $data = $query->orderBy('updated_at', $this->sort)->paginate(10);

        $categories = CxIssue::select('category')->whereNotNull('category')->distinct()->pluck('category');
        $masterServices = Service::all();
        $masterCategories = Service::select('category')->distinct()->pluck('category');

        return view('livewire.cs.follow-up-closing', [
            'data' => $data,
            'activeCount' => $activeCount,
            'categories' => $categories,
            'masterServices' => $masterServices,
            'masterCategories' => $masterCategories
        ])->layout('layouts.app');
    }
}
