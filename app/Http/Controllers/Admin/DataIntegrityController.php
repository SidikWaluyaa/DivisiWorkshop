<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use App\Models\Service;
use App\Models\Complaint;
use App\Models\CsLead;
use App\Models\CsQuotation;
use App\Models\CsSpk;
use App\Models\OTO;
use App\Models\MaterialRequest;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Customer;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DataIntegrityController extends Controller
{
    /**
     * Dashboard Overview
     */
    public function index()
    {
        $stats = [
            'trash' => [
                'workshop' => WorkOrder::onlyTrashed()->count(),
                'cs' => CsLead::onlyTrashed()->count() + CsQuotation::onlyTrashed()->count() + CsSpk::onlyTrashed()->count(),
                'warehouse' => MaterialRequest::onlyTrashed()->count() + Purchase::onlyTrashed()->count(),
                'master' => Service::onlyTrashed()->count() + Material::onlyTrashed()->count() + Customer::onlyTrashed()->count(),
                'cx' => Complaint::onlyTrashed()->count() + OTO::onlyTrashed()->count(),
            ],
            'limbo' => [
                'donasi' => WorkOrder::where('status', WorkOrderStatus::DONASI)->count(),
                'batal' => WorkOrder::where('status', WorkOrderStatus::BATAL)->count(),
                'diantar' => WorkOrder::where('status', WorkOrderStatus::DIANTAR)->count(),
                'wait_verification' => WorkOrder::where('status', WorkOrderStatus::WAITING_VERIFICATION)->count(),
            ],
            'issues' => $this->detectPotentialIssues()
        ];

        return view('admin.data-integrity.index', compact('stats'));
    }

    /**
     * View Global Trash
     */
    public function trash(Request $request)
    {
        $type = $request->get('type', 'work_order');
        $search = $request->get('search');

        if ($type === 'work_order') {
            $model = WorkOrder::class;
        } elseif ($type === 'service') {
            $model = Service::class;
        } elseif ($type === 'complaint') {
            $model = Complaint::class;
        } elseif ($type === 'cs_lead') {
            $model = CsLead::class;
        } elseif ($type === 'cs_quotation') {
            $model = CsQuotation::class;
        } elseif ($type === 'cs_spk') {
            $model = CsSpk::class;
        } elseif ($type === 'oto') {
            $model = OTO::class;
        } elseif ($type === 'material_request') {
            $model = MaterialRequest::class;
        } elseif ($type === 'purchase') {
            $model = Purchase::class;
        } elseif ($type === 'material') {
            $model = Material::class;
        } elseif ($type === 'customer') {
            $model = Customer::class;
        } else {
            $model = null;
        }

        $data = $model ? $model::onlyTrashed()
            ->when($search, function($q) use ($search, $type) {
                if ($type === 'work_order') return $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
                if ($type === 'service') return $q->where('name', 'LIKE', "%{$search}%");
                if ($type === 'complaint') return $q->where(fn($sq) => $sq->where('customer_name', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%"));
                if ($type === 'cs_lead') return $q->where('customer_name', 'LIKE', "%{$search}%")->orWhere('customer_phone', 'LIKE', "%{$search}%");
                if ($type === 'cs_quotation') return $q->where('quotation_number', 'LIKE', "%{$search}%");
                if ($type === 'cs_spk') return $q->where('spk_number', 'LIKE', "%{$search}%");
                if ($type === 'oto') return $q->where('title', 'LIKE', "%{$search}%");
                if ($type === 'material_request') return $q->where('request_number', 'LIKE', "%{$search}%");
                if ($type === 'purchase') return $q->where('po_number', 'LIKE', "%{$search}%");
                if ($type === 'material') return $q->where('name', 'LIKE', "%{$search}%");
                if ($type === 'customer') return $q->where('name', 'LIKE', "%{$search}%")->orWhere('phone', 'LIKE', "%{$search}%");
                return $q;
            })->latest('deleted_at')->paginate(25) : collect();

        return view('admin.data-integrity.trash', compact('data', 'type'));
    }

    /**
     * View Limbo Data (Data that exists but is hidden from mainstream flow)
     */
    public function limbo(Request $request)
    {
        $status = $request->get('status', 'DONASI');
        $search = $request->get('search');

        $orders = WorkOrder::where('status', $status)
            ->when($search, function($q) use ($search) {
                $q->where(fn($sq) => $sq->where('spk_number', 'LIKE', "%{$search}%")->orWhere('customer_name', 'LIKE', "%{$search}%"));
            })
            ->latest()
            ->paginate(25);

        return view('admin.data-integrity.limbo', compact('orders', 'status'));
    }

    /**
     * Internal Health Check Logic
     */
    private function detectPotentialIssues()
    {
        return [
            // CS Health
            'stale_leads' => CsLead::whereNotIn('status', [CsLead::STATUS_CONVERTED, CsLead::STATUS_LOST])
                ->where('updated_at', '<', now()->subDays(7))
                ->count(),
            'expired_quotations' => CsQuotation::where('status', 'PENDING')
                ->where('valid_until', '<', now())
                ->count(),

            // Warehouse Health
            'stale_reception' => WorkOrder::where('status', WorkOrderStatus::SPK_PENDING)
                ->where('created_at', '<', now()->subDays(7))
                ->count(),
            'stale_transit' => WorkOrder::where('status', WorkOrderStatus::DITERIMA)
                ->where('storage_rack_code', 'LIKE', 'RAK-BEFORE%')
                ->where('updated_at', '<', now()->subDays(3))
                ->count(),
            'orphaned_storage' => DB::table('storage_assignments')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('work_orders')
                        ->whereRaw('work_orders.id = storage_assignments.work_order_id');
                })->count(),

            // Workshop Health
            'overdue_production' => WorkOrder::whereNotIn('status', [WorkOrderStatus::SELESAI, WorkOrderStatus::BATAL, WorkOrderStatus::DONASI])
                ->where('estimation_date', '<', now())
                ->count(),
            'stale_assessment' => WorkOrder::where('status', WorkOrderStatus::ASSESSMENT)
                ->where('updated_at', '<', now()->subDays(14))
                ->count(),

            // CX Health
            'pending_complaints' => Complaint::where('status', 'PENDING')
                ->whereNull('admin_notes')
                ->count(),
            'expired_otos' => OTO::where('status', 'PENDING_CUSTOMER')
                ->where('valid_until', '<', now())
                ->count(),
        ];
    }

    /**
     * Robust Restore Logic
     */
    public function restoreMany(Request $request)
    {
        $ids = $request->ids;
        $type = $request->type;

        if (!$ids) return back()->with('error', 'Tidak ada data terpilih.');

        try {
            DB::beginTransaction();

            $model = match($type) {
                'work_order' => WorkOrder::class,
                'service' => Service::class,
                'complaint' => Complaint::class,
                'cs_lead' => CsLead::class,
                'cs_quotation' => CsQuotation::class,
                'cs_spk' => CsSpk::class,
                'oto' => OTO::class,
                'material_request' => MaterialRequest::class,
                'purchase' => Purchase::class,
                'material' => Material::class,
                'customer' => Customer::class,
                default => null
            };

            if (!$model) throw new \Exception('Tipe data tidak valid.');

            $count = 0;
            foreach ($ids as $id) {
                $record = $model::onlyTrashed()->find($id);
                if ($record) {
                    if ($type === 'work_order') {
                        $record->logs()->create([
                            'step' => 'SYSTEM',
                            'action' => 'RESTORED',
                            'description' => 'Dipulihkan secara masal melalui Pusat Kesehatan Data',
                            'user_id' => Auth::id()
                        ]);
                    }
                    $record->restore();
                    
                    // AUDIT LOG
                    
                    // AUDIT LOG
                    Log::info("Data Integrity: User " . Auth::user()->name . " memulihkan {$type} id: {$id}");

                    $count++;
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil memulihkan {$count} data.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulihkan: ' . $e->getMessage());
        }
    }

    /**
     * Deep Force Delete Logic
     */
    public function forceDeleteMany(Request $request)
    {
        $ids = $request->ids;
        $type = $request->type;

        if (!$ids) return back()->with('error', 'Tidak ada data terpilih.');

        try {
            DB::beginTransaction();

            foreach ($ids as $id) {
                if ($type === 'work_order') {
                    $order = WorkOrder::onlyTrashed()->find($id);
                    if ($order) {
                        $order->logs()->forceDelete();
                        foreach ($order->photos as $p) {
                            if ($p->file_path && Storage::disk('public')->exists($p->file_path)) {
                                Storage::disk('public')->delete($p->file_path);
                            }
                            $p->forceDelete();
                        }
                        $order->workOrderServices()->forceDelete();
                        $order->materials()->detach();
                        $order->complaints()->forceDelete();
                        $order->cxIssues()->forceDelete();
                        $order->storageAssignments()->forceDelete();
                        $order->forceDelete();
                    }
                } else {
                    $model = match($type) {
                        'service' => Service::class,
                        'complaint' => Complaint::class,
                        'cs_lead' => CsLead::class,
                        'cs_quotation' => CsQuotation::class,
                        'cs_spk' => CsSpk::class,
                        'oto' => OTO::class,
                        'material_request' => MaterialRequest::class,
                        'purchase' => Purchase::class,
                        'material' => Material::class,
                        'customer' => Customer::class,
                        default => null
                    };
                    if ($model) {
                        $record = $model::onlyTrashed()->find($id);
                        if ($record) {
                            if ($type === 'cs_spk') {
                                $record->items()->delete();
                            }
                            if ($type === 'material_request') {
                                $record->items()->delete();
                            }
                            if ($type === 'cs_quotation') {
                                $record->quotationItems()->delete();
                            }
                            // AUDIT LOG
                            // AUDIT LOG
                            Log::warning("Data Integrity: User " . Auth::user()->name . " menghapus PERMANEN {$type} id: {$id}");

                            $record->forceDelete();
                        }
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Data terpilih berhasil dihapus permanen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus permanen: ' . $e->getMessage());
        }
    }

    /**
     * Permanent Bulk Cleanup per Department
     */
    public function bulkCleanup(Request $request)
    {
        $category = $request->get('category');
        if (!$category) return back()->with('error', 'Kategori tidak valid.');

        $models = match($category) {
            'workshop' => [WorkOrder::class],
            'cs' => [CsLead::class, CsQuotation::class, CsSpk::class],
            'warehouse' => [MaterialRequest::class, Purchase::class],
            'cx' => [Complaint::class, OTO::class],
            'master' => [Service::class, Material::class, Customer::class],
            default => []
        };

        if (empty($models)) return back()->with('error', 'Kategori tidak dikenali.');

        try {
            DB::beginTransaction();
            $totalCount = 0;

            foreach ($models as $modelClass) {
                $trashedIds = $modelClass::onlyTrashed()->pluck('id')->toArray();
                if (!empty($trashedIds)) {
                    // Reuse forceDeleteMany logic by simulating a request
                    $cleanupRequest = new Request([
                        'type' => $this->getTypeFromModel($modelClass),
                        'ids' => $trashedIds
                    ]);
                    $this->forceDeleteMany($cleanupRequest);
                    $totalCount += count($trashedIds);
                }
            }

            DB::commit();
            return back()->with('success', "Pembersihan {$category} selesai. {$totalCount} data dihapus permanen.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal pembersihan: ' . $e->getMessage());
        }
    }

    /**
     * Helper to map model class back to type string
     */
    private function getTypeFromModel($class)
    {
        return match($class) {
            WorkOrder::class => 'work_order',
            Service::class => 'service',
            Complaint::class => 'complaint',
            CsLead::class => 'cs_lead',
            CsQuotation::class => 'cs_quotation',
            CsSpk::class => 'cs_spk',
            OTO::class => 'oto',
            MaterialRequest::class => 'material_request',
            Purchase::class => 'purchase',
            Material::class => 'material',
            Customer::class => 'customer',
            default => 'unknown'
        };
    }
}
