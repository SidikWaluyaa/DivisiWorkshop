<?php

namespace App\Livewire\Cx;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WorkOrder;
use App\Models\CxIssue;
use App\Models\Service;
use App\Models\WorkOrderWarranty;
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
    public $delay_filter = ''; // '', 'stuck_3_days'
    public $est_filter = '';   // '', 'est_3_days'

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
        'delay_filter' => ['except' => ''],
        'est_filter' => ['except' => ''],
    ];

    public function switchTab($tab)
    {
        $this->currentTab = $tab;
        $this->resetFilters();
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'start_date', 'end_date', 'handler_id', 'last_status', 'source', 'category', 'delay_filter', 'est_filter']);
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

    public function setShippingStatus($issueId, $status)
    {
        $issue = CxIssue::find($issueId);
        if ($issue && in_array($status, ['HOLD', 'SEND'])) {
            $issue->update(['shipping_status' => $status]);
            session()->flash('success', "Status pengiriman SPK #{$issue->spk_number} diubah ke {$status}.");
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
        $issue = $order->cxIssues()->where('status', 'OPEN')->latest()->first();

        // If the issue source is GUDANG, it MUST go to ASSESSMENT
        if ($issue && $issue->source === 'GUDANG') {
            $nextStatus = WorkOrderStatus::ASSESSMENT;
        } else {
            // Smart Status Inference to prevent jumping directly to PRODUCTION
            if (!$previousStatus || $previousStatus === WorkOrderStatus::CX_FOLLOWUP || $previousStatus === WorkOrderStatus::HOLD_FOR_CX) {
                if ($issue) {
                    $nextStatus = match ($issue->source) {
                        'WORKSHOP_PREP'   => WorkOrderStatus::PREPARATION,
                        'WORKSHOP_SORTIR' => WorkOrderStatus::SORTIR,
                        'WORKSHOP_PROD'   => WorkOrderStatus::PRODUCTION,
                        'WORKSHOP_QC'     => WorkOrderStatus::QC,
                        default           => WorkOrderStatus::ASSESSMENT,
                    };
                } else {
                    $nextStatus = WorkOrderStatus::ASSESSMENT;
                }
            } else {
                $nextStatus = $previousStatus;
            }
        }

        $order->previous_status = WorkOrderStatus::CX_FOLLOWUP;
        $order->estimation_date = $this->newEstimationDate ?: $order->estimation_date;
        if ($this->actionNotes) {
            $order->technician_notes = trim($order->technician_notes . "\n\n[CX]: " . $this->actionNotes);
        }
        $order->save();

        app(\App\Services\WorkflowService::class)->updateStatus(
            $order,
            $nextStatus,
            $this->actionNotes ? "CX Lanjut: " . $this->actionNotes : "Melanjutkan proses ke " . str_replace('_', ' ', $nextStatus->value),
            Auth::id()
        );

        $statusLabel = str_replace('_', ' ', $nextStatus->value);
        return "Order dilanjutkan kembali ke proses " . $statusLabel . ".";
    }

    protected function handleCancelAction($order)
    {
        app(\App\Services\WorkflowService::class)->updateStatus(
            $order,
            WorkOrderStatus::BATAL,
            $this->actionNotes ? "CX Batal: " . $this->actionNotes : "Order dibatalkan atas kesepakatan",
            Auth::id()
        );
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
                'service_details' => ['is_cx_additional' => true],
                'status' => 'pending',
                'notes' => $service['details'] ?: 'Tambah Jasa ' . $service['display_name'], // Ini yang akan jadi "NB" di Print SPK
                'created_by' => Auth::id()
            ]);
        }
        
        $addedServicesNames = [];
        foreach ($this->addedServices as $service) {
            $addedServicesNames[] = ($service['custom_name'] ?? $service['display_name']) . " (Rp " . number_format($service['cost'], 0, ',', '.') . ")";
        }
        $servicesDetailsStr = implode(', ', $addedServicesNames);
        
        $isFromWorkshop = $issue && str_starts_with($issue->source, 'WORKSHOP_');
        $isFromGudang = ($issue && $issue->source === 'GUDANG') || $order->warehouse_qc_status === 'reject' || !$order->reception_qc_passed;
        $isNotBB = $order->invoice && $order->invoice->status !== 'Belum Bayar';

        if ($isFromGudang) {
            $targetStatus = WorkOrderStatus::ASSESSMENT;
            $order->previous_status = WorkOrderStatus::CX_FOLLOWUP;
            $order->save();

            app(\App\Services\WorkflowService::class)->updateStatus(
                $order,
                $targetStatus,
                "Tambah Jasa (Resolusi CX): " . $servicesDetailsStr . ". Mengarahkan SPK ke Assessment.",
                Auth::id()
            );
            $message = "Layanan tambahan diinput. Karena barang belum melewati assessment, SPK diarahkan ke Assessment.";
        } elseif (!$isFromWorkshop && !$isNotBB) {
            $targetStatus = WorkOrderStatus::WAITING_PAYMENT;
            $order->previous_status = null;
            $order->save();

            app(\App\Services\WorkflowService::class)->updateStatus(
                $order,
                $targetStatus,
                "Tambah Jasa (Resolusi CX): " . $servicesDetailsStr . ". Mengalihkan SPK ke Waiting Payment.",
                Auth::id()
            );
            $message = "Layanan tambahan diinput. Karena barang di luar Workshop dan status pembayaran Belum Bayar, SPK dialihkan ke Waiting Payment.";
        } else {
            // Smart Status Inference for Tambah Jasa
            $previousStatus = $order->previous_status;
            if (!$previousStatus || $previousStatus === WorkOrderStatus::CX_FOLLOWUP || $previousStatus === WorkOrderStatus::HOLD_FOR_CX) {
                $targetStatus = match ($issue->source ?? 'default') {
                    'WORKSHOP_PREP'   => WorkOrderStatus::PREPARATION,
                    'WORKSHOP_SORTIR' => WorkOrderStatus::SORTIR,
                    'WORKSHOP_PROD'   => WorkOrderStatus::PRODUCTION,
                    'WORKSHOP_QC'     => WorkOrderStatus::QC,
                    default           => WorkOrderStatus::ASSESSMENT, // Safest default is Assessment
                };
            } else {
                $targetStatus = $previousStatus;
            }
            
            $order->previous_status = WorkOrderStatus::CX_FOLLOWUP;
            $order->save();

            app(\App\Services\WorkflowService::class)->updateStatus(
                $order,
                $targetStatus,
                "Tambah Jasa (Resolusi CX): " . $servicesDetailsStr . ". Mengembalikan ke status " . str_replace('_', ' ', $targetStatus->value),
                Auth::id()
            );
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
        
        if ($issue) {
            $issue->update($payload);
            
            // Close other open issues with an auto-resolve note instead of duplicating
            CxIssue::where('work_order_id', $order->id)
                ->where('status', 'OPEN')
                ->where('id', '!=', $issue->id)
                ->update([
                    'status' => 'RESOLVED',
                    'resolved_by' => $user->id,
                    'resolved_at' => now(),
                    'resolution_notes' => 'Diselesaikan otomatis bersamaan dengan kendala utama.',
                    'resolution_type' => $this->actionType
                ]);
        } else {
            CxIssue::where('work_order_id', $order->id)
                ->where('status', 'OPEN')
                ->update($payload);
        }
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

    public function getFilteredQuery()
    {
        $user = Auth::user();

        if ($this->currentTab === 'history') {
            $query = CxIssue::where('status', 'RESOLVED')
                ->with(['workOrder.cxHandler', 'resolver', 'reporter']);
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
            
            $query->orderBy('resolved_at', $this->sort);
        } 
        elseif ($this->currentTab === 'cancelled') {
            $query = WorkOrder::where('status', WorkOrderStatus::BATAL->value)
                ->with(['logs', 'cxIssues' => fn($q) => $q->latest(), 'cxHandler']);
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('spk_number', 'like', '%' . $this->search . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            }
            if ($this->start_date) $query->whereDate('updated_at', '>=', $this->start_date);
            if ($this->end_date) $query->whereDate('updated_at', '<=', $this->end_date);
            
            $query->orderBy('updated_at', $this->sort);
        } 
        elseif ($this->currentTab === 'warranty') {
            $query = WorkOrderWarranty::where('status', 'OPEN')
                ->with(['workOrder', 'reworkWorkOrder', 'creator']);
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('garansi_spk_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('workOrder', function($sq) {
                          $sq->where('customer_name', 'like', '%' . $this->search . '%')
                            ->orWhere('customer_phone', 'like', '%' . $this->search . '%')
                            ->orWhere('spk_number', 'like', '%' . $this->search . '%');
                      });
                });
            }
            if ($this->start_date) $query->whereDate('created_at', '>=', $this->start_date);
            if ($this->end_date) $query->whereDate('created_at', '<=', $this->end_date);
            
            $query->orderBy('created_at', $this->sort);
        }
        else {
            $query = WorkOrder::where(function($q) {
                $q->whereIn('status', [WorkOrderStatus::CX_FOLLOWUP->value, WorkOrderStatus::HOLD_FOR_CX->value])
                  ->orWhereHas('cxIssues', function($sq) {
                      $sq->where('status', 'OPEN')
                         ->where('category', 'like', 'Revisi %')
                         ->where(function($subQ) {
                             $subQ->where(function($q1) {
                                 $q1->whereIn('category', ['Revisi PREPARATION', 'REVISI PREPARATION'])
                                    ->where('work_orders.status', 'PREPARATION');
                             })
                             ->orWhere(function($q1) {
                                 $q1->whereIn('category', ['Revisi SORTIR', 'REVISI SORTIR'])
                                    ->where('work_orders.status', 'SORTIR');
                             })
                             ->orWhere(function($q1) {
                                 $q1->whereIn('category', ['Revisi PRODUCTION', 'REVISI PRODUCTION'])
                                    ->where('work_orders.status', 'PRODUCTION');
                             })
                             ->orWhere(function($q1) {
                                 $q1->whereIn('category', ['Revisi QC', 'REVISI QC'])
                                    ->where('work_orders.status', 'QC');
                             });
                         });
                  });
            })
            ->whereNotIn('status', [
                WorkOrderStatus::SELESAI->value,
                WorkOrderStatus::DIANTAR->value,
                WorkOrderStatus::HISTORY->value,
                WorkOrderStatus::BATAL->value
            ])
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
            
            // Apply Estimation Filter (<= 3 Days & Overdue)
            if ($this->est_filter === 'est_3_days') {
                $query->where(function($q) {
                    $q->where(function($sq) {
                        $sq->whereNotNull('new_estimation_date')
                           ->where('new_estimation_date', '<=', now()->addDays(3));
                    })->orWhere(function($sq) {
                        $sq->whereNull('new_estimation_date')
                           ->whereNotNull('estimation_date')
                           ->where('estimation_date', '<=', now()->addDays(3));
                    });
                });
            }

            // Exclude GUDANG source issues from the general CX list unless delay_filter is active.
            // Allow WorkOrders with NO open issues to show up if their status is CX_FOLLOWUP/HOLD_FOR_CX so they don't get stuck in limbo!
            $query->where(function($mainQ) {
                $mainQ->whereHas('cxIssues', function($q) {
                    $q->where('status', 'OPEN');
                    
                    if ($this->delay_filter === 'stuck_3_days') {
                        $q->where('created_at', '<=', now()->subDays(3));
                    } else {
                        if ($this->source) {
                            $q->where('source', $this->source === 'WS' ? 'LIKE' : '=', $this->source === 'WS' ? 'WORKSHOP_%' : $this->source);
                        } else {
                            $q->where('source', '!=', 'GUDANG');
                        }
                    }
                })
                ->orWhereDoesntHave('cxIssues', function($q) {
                    $q->where('status', 'OPEN');
                });
            });
            
            // Apply sorting based on active filters to prioritize critical items
            if ($this->delay_filter === 'stuck_3_days') {
                $query->orderBy(
                    \App\Models\CxIssue::select('created_at')
                        ->whereColumn('work_order_id', 'work_orders.id')
                        ->where('status', 'OPEN')
                        ->latest()
                        ->limit(1),
                    'asc'
                );
            } elseif ($this->est_filter === 'est_3_days') {
                $query->orderByRaw('COALESCE(new_estimation_date, estimation_date) ASC');
            } else {
                $query->orderBy('entry_date', $this->sort);
            }
        }

        return $query;
    }

    public function exportPdf()
    {
        $data = $this->getFilteredQuery()->get();
        $tab = $this->currentTab;
        
        $summary = [
            'total' => $data->count(),
            'open' => 0,
            'resolved' => 0,
            'hold' => 0
        ];
        
        foreach ($data as $item) {
            if ($item instanceof WorkOrder) {
                $openIssue = $item->cxIssues->first();
            } else {
                $openIssue = $item;
            }
            
            if ($openIssue) {
                if ($openIssue->status === 'RESOLVED') {
                    $summary['resolved']++;
                } else {
                    $summary['open']++;
                }
                
                if ($openIssue->shipping_status === 'HOLD') {
                    $summary['hold']++;
                }
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cx.pdf.followup-report', [
            'data' => $data,
            'tab' => $tab,
            'summary' => $summary
        ]);
        
        $filename = 'Laporan_Followup_CX_' . $tab . '_' . date('Y-m-d_His') . '.pdf';
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }

    public function exportExcel()
    {
        $data = $this->getFilteredQuery()->get();
        $tab = $this->currentTab;
        $filename = 'Laporan_Followup_CX_' . $tab . '_' . date('Y-m-d_His') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CxFollowupExport($data), $filename);
    }

    public function render()
    {
        $user = Auth::user();
        
        $activeCount = WorkOrder::getCxActiveCount();

        $data = $this->getFilteredQuery()->paginate($this->currentTab === 'active' ? 10 : 15);

        $categories = CxIssue::select('category')->whereNotNull('category')->distinct()->pluck('category');
        $masterServices = Service::all();
        $masterCategories = Service::select('category')->distinct()->pluck('category');
        $warrantyCount = WorkOrderWarranty::where('status', 'OPEN')->count();

        return view('livewire.cx.index', [
            'data' => $data,
            'activeCount' => $activeCount,
            'warrantyCount' => $warrantyCount,
            'categories' => $categories,
            'masterServices' => $masterServices,
            'masterCategories' => $masterCategories
        ]);
    }
}
