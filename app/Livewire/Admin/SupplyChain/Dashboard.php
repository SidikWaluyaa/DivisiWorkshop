<?php

namespace App\Livewire\Admin\SupplyChain;

use App\Models\Material;
use App\Models\MaterialTransaction;
use App\Models\WorkOrder;
use App\Models\MaterialRequest;
use App\Enums\WorkOrderStatus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    protected $listeners = ['refreshDashboard' => '$refresh'];

    public function getStatsProperty()
    {
        $currentValuation = Material::sum(DB::raw('stock * price'));
        
        // Calculate valuation 30 days ago (approximation using JOIN for performance)
        $txIn = MaterialTransaction::where('material_transactions.type', 'IN')
            ->where('material_transactions.created_at', '>=', now()->subDays(30))
            ->join('materials', 'material_transactions.material_id', '=', 'materials.id')
            ->sum(DB::raw('material_transactions.quantity * materials.price'));

        $txOut = MaterialTransaction::where('material_transactions.type', 'OUT')
            ->where('material_transactions.created_at', '>=', now()->subDays(30))
            ->join('materials', 'material_transactions.material_id', '=', 'materials.id')
            ->sum(DB::raw('material_transactions.quantity * materials.price'));
        
        $prevValuation = $currentValuation - $txIn + $txOut;
        $trend = $prevValuation > 0 ? (($currentValuation - $prevValuation) / $prevValuation) * 100 : 0;

        // NEW: Monthly Purchasing Stats
        $purchasedThisMonthValue = MaterialTransaction::where('material_transactions.type', 'IN')
            ->where('material_transactions.reference_type', 'MaterialRequest')
            ->where('material_transactions.created_at', '>=', now()->startOfMonth())
            ->join('materials', 'material_transactions.material_id', '=', 'materials.id')
            ->sum(DB::raw('material_transactions.quantity * materials.price'));

        return [
            'total_materials' => Material::count(),
            'low_stock_count' => Material::whereColumn('stock', '<=', 'min_stock')->count(),
            'total_valuation' => $currentValuation,
            'valuation_trend' => round($trend, 1),
            'pending_requests' => MaterialRequest::where('status', 'PENDING')->count(),
            'total_purchased_value' => $purchasedThisMonthValue,
            'total_purchased_count' => MaterialRequest::where('status', 'PURCHASED')->where('updated_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    public function getTopConsumedProperty()
    {
        // Filter: Hanya yang sudah dipakai oleh SPK dan sudah ke status PRODUCTION atau lebih tinggi
        $productionStatuses = [
            WorkOrderStatus::PRODUCTION->value,
            WorkOrderStatus::QC->value,
            WorkOrderStatus::SELESAI->value,
            WorkOrderStatus::DIANTAR->value,
        ];

        $top = MaterialTransaction::where('material_transactions.type', 'OUT')
            ->where('material_transactions.reference_type', 'WorkOrder')
            ->where('material_transactions.created_at', '>=', now()->subDays(30))
            ->join('work_orders', 'material_transactions.reference_id', '=', 'work_orders.id')
            ->whereIn('work_orders.status', $productionStatuses)
            ->select('material_transactions.material_id', DB::raw('SUM(material_transactions.quantity) as total_qty'))
            ->groupBy('material_transactions.material_id')
            ->with(['material' => function($q) {
                $q->withTrashed();
            }])
            ->orderByDesc('total_qty')
            ->take(4)
            ->get();

        $max = $top->first() ? $top->first()->total_qty : 1;

        return $top->map(function($item) use ($max) {
            $ratio = ($item->total_qty / $max) * 100;
            $status = 'Teralokasi';
            $color = 'bg-[#22AF85]';
            
            if ($ratio > 85) {
                $status = 'Efisiensi Puncak';
            } elseif ($item->material && $item->material->isLowStock()) {
                $status = 'Kritikal';
                $color = 'bg-[#FFC232]';
            } elseif (!$item->material) {
                $status = 'Archived';
                $color = 'bg-gray-400';
            }

            $item->ratio = $ratio;
            $item->status_label = $status;
            $item->color_class = $color;
            return $item;
        });
    }

    public function getAuditLedgerProperty()
    {
        return MaterialTransaction::with([
                'material' => function($q) { $q->withTrashed(); },
                'user'
            ])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($tx) {
                $refDetail = 'Matriks Sistem';
                
                // 1. Resolve Reference
                if ($tx->reference_type === 'WorkOrder') {
                    $wo = WorkOrder::find($tx->reference_id);
                    if ($wo) {
                        $refDetail = "SPK #{$wo->spk_number} ({$wo->customer_name})";
                    }
                } elseif ($tx->reference_type === 'MaterialRequest') {
                    $req = MaterialRequest::find($tx->reference_id);
                    if ($req) {
                        $refDetail = "PO #{$req->request_number}";
                    }
                }

                $tx->ref_detail = $refDetail;

                // 2. Set Labels (System Integration Focus)
                $tx->event_label = match(true) {
                    str_contains($tx->notes, '[ADJUSTMENT]') => 'AUDIT / PENYESUAIAN',
                    $tx->type === 'IN' && $tx->reference_type === 'MaterialRequest' => 'BARANG MASUK (PO)',
                    $tx->type === 'OUT' && $tx->reference_type === 'WorkOrder' => 'BARANG KELUAR (SPK)',
                    $tx->type === 'IN' => 'MATERIAL MASUK',
                    $tx->type === 'OUT' => 'MATERIAL KELUAR',
                    default => 'PENYESUAIAN STOK'
                };
                
                $tx->status_label = match($tx->type) {
                    'IN' => 'TERVERIFIKASI',
                    'OUT' => 'DIPROSES',
                    default => 'MANUAL'
                };

                return $tx;
            });
    }

    public function getBottlenecksProperty()
    {
        return WorkOrder::waitingForMaterials()
            ->with(['materials' => function($q) {
                $q->where('work_order_materials.status', 'REQUESTED');
            }])
            ->orderBy('priority', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.supply-chain.dashboard', [
            'stats' => $this->stats,
            'topConsumed' => $this->topConsumed,
            'auditLedger' => $this->auditLedger,
            'bottlenecks' => $this->bottlenecks,
        ])->layout('layouts.app');
    }
}
