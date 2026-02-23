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
use App\Models\StorageRack;
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
                'warehouse' => MaterialRequest::onlyTrashed()->count() + Purchase::onlyTrashed()->count() + StorageRack::onlyTrashed()->count(),
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
        $type = $request->get('type');
        $category = $request->get('category');

        // If category is provided but no type, auto-select the best type
        if (!$type && $category) {
            $type = match($category) {
                'workshop' => 'work_order',
                'cs' => CsLead::onlyTrashed()->exists() ? 'cs_lead' : (CsQuotation::onlyTrashed()->exists() ? 'cs_quotation' : 'cs_spk'),
                'warehouse' => MaterialRequest::onlyTrashed()->exists() ? 'material_request' : 
                               (Purchase::onlyTrashed()->exists() ? 'purchase' : 'storage_rack'),
                'cx', 'master' => Complaint::onlyTrashed()->exists() ? 'complaint' : 
                                 (OTO::onlyTrashed()->exists() ? 'oto' : 
                                 (Service::onlyTrashed()->exists() ? 'service' : 
                                 (Material::onlyTrashed()->exists() ? 'material' : 'customer'))),
                default => 'work_order'
            };
        }

        // Global default if no type/category and workshop is empty
        if (!$type && !$category && WorkOrder::onlyTrashed()->count() === 0) {
            if (CsLead::onlyTrashed()->exists()) $type = 'cs_lead';
            elseif (MaterialRequest::onlyTrashed()->exists()) $type = 'material_request';
            elseif (Purchase::onlyTrashed()->exists()) $type = 'purchase';
            elseif (StorageRack::onlyTrashed()->exists()) $type = 'storage_rack';
            elseif (Complaint::onlyTrashed()->exists()) $type = 'complaint';
            elseif (OTO::onlyTrashed()->exists()) $type = 'oto';
            elseif (Service::onlyTrashed()->exists()) $type = 'service';
            elseif (Material::onlyTrashed()->exists()) $type = 'material';
            elseif (Customer::onlyTrashed()->exists()) $type = 'customer';
        }

        // Default type if still none
        if (!$type) $type = 'work_order';

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
        } elseif ($type === 'storage_rack') {
            $model = StorageRack::class;
        } else {
            $model = null;
        }

        // Final check: if the selected type is empty but we are in a category, try another
        if ($category && (!$model || $model::onlyTrashed()->count() === 0)) {
            $altType = match($category) {
                'cs' => CsQuotation::onlyTrashed()->exists() ? 'cs_quotation' : (CsSpk::onlyTrashed()->exists() ? 'cs_spk' : null),
                'warehouse' => Purchase::onlyTrashed()->exists() ? 'purchase' : null,
                'cx' => OTO::onlyTrashed()->exists() ? 'oto' : null,
                'master' => Material::onlyTrashed()->exists() ? 'material' : (Customer::onlyTrashed()->exists() ? 'customer' : null),
                default => null
            };
            if ($altType) {
                return redirect()->route('admin.data-integrity.trash', ['type' => $altType, 'category' => $category]);
            }
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
                if ($type === 'storage_rack') return $q->where('code', 'LIKE', "%{$search}%");
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
            'unlinked_work_orders' => WorkOrder::whereNotNull('customer_phone')
                ->where('customer_phone', '!=', '')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('customers')
                        ->whereRaw('customers.phone = work_orders.customer_phone');
                })->count(),
            'missing_phone_work_orders' => WorkOrder::where(function($q) {
                $q->whereNull('customer_phone')->orWhere('customer_phone', '');
            })->count(),
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
                'storage_rack' => StorageRack::class,
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
                        'storage_rack' => StorageRack::class,
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
            'warehouse' => [MaterialRequest::class, Purchase::class, StorageRack::class],
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
     * View System Logs
     */
    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $lines = [];
        
        if (file_exists($logFile)) {
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            
            $limit = 200;
            $start = max(0, $totalLines - $limit);
            
            $file->seek($start);
            while (!$file->eof()) {
                $line = trim($file->current());
                if ($line) $lines[] = $line;
                $file->next();
            }
            
            $lines = array_reverse($lines);
        }
        
        return view('admin.data-integrity.logs', compact('lines'));
    }

    /**
     * Clear System Logs
     */
    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return back()->with('success', 'Log sistem berhasil dibersihkan.');
        }
        return back()->with('error', 'File log tidak ditemukan.');
    }

    /**
     * Repair broken links between Customers and WorkOrders due to phone normalization
     */
    public function repairCustomerLinks(Request $request)
    {
        $deepRepair = $request->has('deep_repair');
        
        try {
            DB::beginTransaction();
            
            // Log target issues count for comparison
            $unlinkedCountInView = $request->input('target_count', 0);
            Log::info("Repair Tool Started. User sees unlinked orders. Deep Repair: " . ($deepRepair ? 'ON' : 'OFF'));

            // 1. Get unique phones from work orders that are currently unlinked
            $rawPhones = WorkOrder::select('customer_phone', 'customer_name', 'customer_email', 'customer_address')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('customers')
                        ->whereRaw('customers.phone = work_orders.customer_phone');
                })
                ->whereNotNull('customer_phone')
                ->where('customer_phone', '!=', '')
                ->groupBy('customer_phone')
                ->get();
            
            $repairedCount = 0;
            $createdCount = 0;
            $details = [];

            // Repair by phone normalization
            foreach ($rawPhones as $row) {
                $oldPhone = $row->customer_phone;
                $normalized = \App\Helpers\PhoneHelper::normalize($oldPhone);
                
                if ($normalized) {
                    $customer = Customer::where('phone', $normalized)->first();
                    if ($customer) {
                        $affected = WorkOrder::where('customer_phone', $oldPhone)
                            ->update(['customer_phone' => $normalized]);
                        $repairedCount += $affected;
                        $details[] = "Repaired by Phone: {$oldPhone} -> {$normalized}";
                    } 
                    elseif ($deepRepair) {
                        $existingNow = Customer::where('phone', $normalized)->first();
                        if (!$existingNow) {
                            Customer::create([
                                'name' => $row->customer_name ?? 'Customer Auto-Created',
                                'phone' => $normalized,
                                'email' => $row->customer_email,
                                'address' => $row->customer_address,
                            ]);
                            $createdCount++;
                        }
                        $affected = WorkOrder::where('customer_phone', $oldPhone)
                            ->update(['customer_phone' => $normalized]);
                        $repairedCount += $affected;
                        $details[] = "Deep Repair (New Customer): {$oldPhone} -> {$normalized}";
                    }
                }
            }

            // 2. Extra Repair for NULL Phones (Only if Deep Repair is ON)
            if ($deepRepair) {
                // Get all unlinked records with empty phones
                $unlinkedWorkOrders = WorkOrder::where(function($q) {
                        $q->whereNull('customer_phone')->orWhere('customer_phone', '');
                    })
                    ->whereNotNull('customer_name')
                    ->get();
                
                // Track names we've tried to avoid redundant queries
                $processedNames = [];

                foreach ($unlinkedWorkOrders as $wo) {
                    $name = trim($wo->customer_name);
                    if (empty($name) || in_array($name, $processedNames)) continue;

                    // A. Try to find in Master Customers (Case-Insensitive)
                    $customer = Customer::where('name', $name)
                        ->orWhere('name', 'LIKE', $name)
                        ->first();
                    
                    if ($customer && $customer->phone) {
                        $affected = WorkOrder::where('customer_name', $wo->customer_name)
                            ->where(function($q) {
                                $q->whereNull('customer_phone')->orWhere('customer_phone', '');
                            })
                            ->update(['customer_phone' => $customer->phone]);
                        
                        $repairedCount += $affected;
                        $details[] = "Repaired by Master Name: '{$name}' -> Found Customer Phone {$customer->phone}";
                        $processedNames[] = $name;
                        continue;
                    }

                    // B. Cross-Reference: Try to find Phone from OTHER WorkOrders with the same name
                    $otherWo = WorkOrder::where('customer_name', $wo->customer_name)
                        ->whereNotNull('customer_phone')
                        ->where('customer_phone', '!=', '')
                        ->first();
                    
                    if ($otherWo) {
                        $normalizedOther = \App\Helpers\PhoneHelper::normalize($otherWo->customer_phone);
                        if ($normalizedOther) {
                            $affected = WorkOrder::where('customer_name', $wo->customer_name)
                                ->where(function($q) {
                                    $q->whereNull('customer_phone')->orWhere('customer_phone', '');
                                })
                                ->update(['customer_phone' => $normalizedOther]);
                            
                            $repairedCount += $affected;
                            $details[] = "Cross-Referenced: Found phone {$normalizedOther} for '{$name}' from other SPKs";
                            $processedNames[] = $name;
                        }
                    }
                }
            }
            
            DB::commit();
            
            if (!empty($details)) {
                Log::info("Repair Tool Success Summary: Reconnected {$repairedCount} orders, Created {$createdCount} customers.");
            }

            $msg = "Pemeriksaan selesai. Berhasil menyambungkan kembali {$repairedCount} riwayat pesanan.";
            if ($createdCount > 0) $msg .= " Mengaktifkan kembali {$createdCount} profil customer baru.";
            
            return back()->with('success', $msg . " Cek log untuk detail.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Repair Tool Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
            return back()->with('error', "Gagal sinkronisasi data: " . $e->getMessage());
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
            StorageRack::class => 'storage_rack',
            default => 'unknown'
        };
    }
}
