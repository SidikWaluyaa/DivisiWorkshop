<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanItem;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuratJalanController extends Controller
{
    /**
     * Display list of Surat Jalan (FR-10.1, SRS §3.5)
     */
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'sortir_to_produksi');

        $query = SuratJalan::with([
            'pengirim', 
            'penerima', 
            'items.workOrder.customer', 
            'items.workOrder.workOrderServices.service',
            'items.workOrder.prodUpperBy',
            'items.workOrder.prodSolBy',
            'items.workOrder.qcJahitBy',
            'items.workOrder.prodCleaningBy',
        ]);

        if (!empty($jenis) && $jenis !== 'all') {
            $query->where('jenis_serah_terima', $jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suratJalanList = $query->latest()->paginate(15);

        // Fetch candidate SPKs for this specific transfer type
        $availableOrders = collect();
        if ($jenis === 'sortir_to_produksi') {
            $availableOrders = WorkOrder::whereIn('status', [\App\Enums\WorkOrderStatus::SORTIR, \App\Enums\WorkOrderStatus::PRODUCTION])
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'SORTIR')
                       ->where('action', 'CLASSIFICATION_COMPLETED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'sortir_to_produksi');
                })
                ->with(['customer', 'workOrderServices.service'])
                ->get();
        } elseif ($jenis === 'produksi_to_post_qc') {
            $availableOrders = WorkOrder::whereIn('status', [\App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC])
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'PRODUCTION')
                       ->where('action', 'PRODUCTION_APPROVED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'produksi_to_post_qc');
                })
                ->with(['customer', 'workOrderServices.service', 'prodUpperBy', 'prodSolBy', 'qcJahitBy', 'prodCleaningBy'])
                ->get();
        }

        // Compute metrics
        $baseMetricQuery = SuratJalan::query();
        if (!empty($jenis) && $jenis !== 'all') {
            $baseMetricQuery->where('jenis_serah_terima', $jenis);
        }

        $totalCount = (clone $baseMetricQuery)->count();
        $dikirimCount = (clone $baseMetricQuery)->where('status', 'DIKIRIM')->count();
        $diterimaCount = (clone $baseMetricQuery)->where('status', 'DITERIMA')->count();
        $candidateCount = $availableOrders->count();

        return view('surat-jalan.index', compact(
            'suratJalanList', 
            'jenis', 
            'availableOrders', 
            'totalCount', 
            'dikirimCount', 
            'diterimaCount', 
            'candidateCount'
        ));
    }

    /**
     * Show form to create new Surat Jalan
     */
    public function create(Request $request)
    {
        $jenis = $request->get('jenis', 'sortir_to_produksi');

        // Fetch candidate SPKs based on transfer type
        $availableOrders = collect();
        if ($jenis === 'sortir_to_produksi') {
            $availableOrders = WorkOrder::whereIn('status', [\App\Enums\WorkOrderStatus::SORTIR, \App\Enums\WorkOrderStatus::PRODUCTION])
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'SORTIR')
                       ->where('action', 'CLASSIFICATION_COMPLETED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'sortir_to_produksi');
                })
                ->with(['customer', 'workOrderServices.service'])
                ->get();
        } elseif ($jenis === 'produksi_to_post_qc') {
            $availableOrders = WorkOrder::whereIn('status', [\App\Enums\WorkOrderStatus::PRODUCTION, \App\Enums\WorkOrderStatus::QC])
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'PRODUCTION')
                       ->where('action', 'PRODUCTION_APPROVED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'produksi_to_post_qc');
                })
                ->with(['customer', 'workOrderServices.service', 'prodUpperBy', 'prodSolBy', 'qcJahitBy', 'prodCleaningBy'])
                ->get();
        }

        $nomorSurat = SuratJalan::generateNomorSurat($jenis);

        return view('surat-jalan.create', compact('jenis', 'availableOrders', 'nomorSurat'));
    }

    /**
     * Store a new Surat Jalan and link SPK items
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_serah_terima' => 'required|string|in:sortir_to_produksi,produksi_to_post_qc,post_qc_to_office',
            'work_order_ids' => 'required|array|min:1',
            'work_order_ids.*' => 'exists:work_orders,id',
            'catatan' => 'nullable|string',
        ]);

        $suratJalan = DB::transaction(function () use ($request) {
            $nomorSurat = SuratJalan::generateNomorSurat($request->jenis_serah_terima);

            $suratJalan = SuratJalan::create([
                'nomor_surat' => $nomorSurat,
                'jenis_serah_terima' => $request->jenis_serah_terima,
                'pengirim_id' => Auth::id() ?? 1,
                'dikirim_at' => now(),
                'status' => 'DIKIRIM',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->work_order_ids as $workOrderId) {
                SuratJalanItem::create([
                    'surat_jalan_id' => $suratJalan->id,
                    'work_order_id' => $workOrderId,
                    'kondisi_serah_terima' => 'Baik / Sesuai Fisik',
                ]);

                // Record audit log on WorkOrder
                $wo = WorkOrder::find($workOrderId);
                if ($wo) {
                    $wo->logs()->create([
                        'user_id' => Auth::id() ?? 1,
                        'step' => 'HANDOVER',
                        'action' => 'ISSUE_SURAT_JALAN',
                        'description' => "Dibuatkan Surat Jalan {$suratJalan->nomor_surat} ({$suratJalan->jenis_serah_terima})",
                    ]);
                }
            }

            return $suratJalan;
        });

        return redirect()->route('surat-jalan.show', $suratJalan->id)
            ->with('success', "Surat Jalan {$suratJalan->nomor_surat} berhasil diterbitkan.");
    }

    /**
     * Show detail of Surat Jalan
     */
    public function show($id)
    {
        $suratJalan = SuratJalan::with([
            'pengirim', 
            'penerima', 
            'items.workOrder.materials',
            'items.workOrder.services',
            'items.workOrder.lead',
            'items.workOrder.customer',
            'items.workOrder.workOrderServices.service',
            'items.workOrder.prodUpperBy',
            'items.workOrder.prodSolBy',
            'items.workOrder.qcJahitBy',
            'items.workOrder.prodCleaningBy',
        ])->findOrFail($id);

        // Self-healing: Sync any SPK materials whose MaterialRequest is RECEIVED or arrived
        foreach ($suratJalan->items as $item) {
            if ($item->workOrder) {
                $wo = $item->workOrder;
                $hasArrivedMR = \App\Models\MaterialRequestItem::where('work_order_id', $wo->id)
                    ->whereHas('materialRequest', function($q) {
                        $q->where('status', 'RECEIVED');
                    })->exists() || !empty($wo->material_arrival_date);

                if ($hasArrivedMR) {
                    \Illuminate\Support\Facades\DB::table('work_order_materials')
                        ->where('work_order_id', $wo->id)
                        ->where('status', 'REQUESTED')
                        ->update(['status' => 'RECEIVED']);

                    $hasUnfulfilled = $wo->materials()
                        ->wherePivot('status', 'REQUESTED')
                        ->exists();

                    if (!$hasUnfulfilled && $wo->perlu_belanja) {
                        $wo->update(['perlu_belanja' => false]);
                    }
                }
            }
        }

        // Reload fresh relations
        $suratJalan->load([
            'items.workOrder.materials',
            'items.workOrder.services',
            'items.workOrder.workOrderServices.service',
            'items.workOrder.prodUpperBy',
            'items.workOrder.prodSolBy',
            'items.workOrder.qcJahitBy',
            'items.workOrder.prodCleaningBy',
        ]);

        $technicians = User::where(function($q) {
            $q->where('role', 'teknisi')
              ->orWhere('role', 'admin')
              ->orWhere('role', 'superadmin')
              ->orWhere('role', 'owner');
        })->where('is_active', true)->orderBy('name')->get();

        return view('surat-jalan.show', compact('suratJalan', 'technicians'));
    }

    /**
     * Print preview / PDF view of Surat Jalan (FR-10.1)
     */
    public function print($id)
    {
        $suratJalan = SuratJalan::with([
            'pengirim', 
            'penerima', 
            'items.workOrder.materials',
            'items.workOrder.services', 
            'items.workOrder.lead',
            'items.workOrder.customer',
            'items.workOrder.workOrderServices.service',
            'items.workOrder.prodUpperBy',
            'items.workOrder.prodSolBy',
            'items.workOrder.qcJahitBy',
            'items.workOrder.prodCleaningBy',
        ])->findOrFail($id);

        return view('surat-jalan.print', compact('suratJalan'));
    }

    /**
     * Complete or update technician for a specific station directly from Surat Jalan
     */
    public function completeStationTechnician(Request $request, $id)
    {
        $request->validate([
            'work_order_id' => 'required|exists:work_orders,id',
            'station' => 'required|in:prod_upper,prod_sol,qc_jahit,prod_cleaning',
            'technician_id' => 'required|exists:users,id',
        ]);

        $wo = WorkOrder::findOrFail($request->work_order_id);
        $tech = User::findOrFail($request->technician_id);

        $stationFieldBy = $request->station . '_by';
        $stationFieldAt = $request->station . '_completed_at';
        $stationFieldStarted = $request->station . '_started_at';

        $wo->update([
            $stationFieldBy => $tech->id,
            $stationFieldStarted => $wo->$stationFieldStarted ?: now(),
            $stationFieldAt => now(),
        ]);

        // Also assign technician to matching work order services
        $stationCodeMap = [
            'prod_upper' => 'UPPER',
            'prod_sol' => 'SOLING',
            'qc_jahit' => 'JAHIT',
            'prod_cleaning' => 'TREATMENT',
        ];
        $targetStation = $stationCodeMap[$request->station] ?? '';

        foreach ($wo->workOrderServices as $svc) {
            $code = \App\Helpers\ProductionStationHelper::getStationCode($svc->category_name ?? $svc->service?->name ?? '');
            if ($code === $targetStation || ($targetStation === 'JAHIT' && in_array($code, ['SOLING', 'UPPER', 'JAHIT']))) {
                $svc->update(['technician_id' => $tech->id]);
            }
        }

        $stationLabelMap = [
            'prod_upper' => 'Reparasi Upper',
            'prod_sol' => 'Reparasi Sol',
            'qc_jahit' => 'QC Jahit',
            'prod_cleaning' => 'Treatment/Cleaning',
        ];
        $label = $stationLabelMap[$request->station] ?? $request->station;

        $wo->logs()->create([
            'user_id' => Auth::id() ?? 1,
            'step' => 'PRODUCTION',
            'action' => 'TECHNICIAN_ASSIGNED',
            'description' => "Teknisi stasiun {$label} dicatat & diselesaikan: {$tech->name} via Surat Jalan #{$id}",
        ]);

        return redirect()->back()->with('success', "Stasiun {$label} untuk SPK #{$wo->spk_number} berhasil diselesaikan oleh teknisi {$tech->name}!");
    }

    /**
     * Mark Surat Jalan as received by target stage Admin and transition SPKs
     */
    public function markAsReceived(Request $request, $id)
    {
        $suratJalan = SuratJalan::with([
            'items.workOrder.materials',
            'items.workOrder.workOrderServices.service',
            'items.workOrder.prodUpperBy',
            'items.workOrder.prodSolBy',
            'items.workOrder.qcJahitBy',
            'items.workOrder.prodCleaningBy',
        ])->findOrFail($id);

        if ($suratJalan->status === 'DITERIMA') {
            return redirect()->back()->with('info', "Surat Jalan {$suratJalan->nomor_surat} sudah pernah diterima sebelumnya.");
        }

        try {
            DB::transaction(function () use ($suratJalan) {
                $suratJalan->update([
                    'penerima_id' => Auth::id() ?? 1,
                    'diterima_at' => now(),
                    'status' => 'DITERIMA',
                ]);

                $workflowService = app(\App\Services\WorkflowService::class);
                $materialService = app(\App\Services\MaterialManagementService::class);

                foreach ($suratJalan->items as $item) {
                    if (!$item->workOrder) continue;
                    $wo = $item->workOrder;

                    if ($suratJalan->jenis_serah_terima === 'sortir_to_produksi') {
                        if ($wo->status === \App\Enums\WorkOrderStatus::SORTIR) {
                            $workflowService->updateStatus($wo, \App\Enums\WorkOrderStatus::PRODUCTION);
                        }

                        // Material handover deduction (KELUAR) for Produksi usage
                        if ($wo->materials && $wo->materials->isNotEmpty()) {
                            foreach ($wo->materials as $mat) {
                                $qty = $mat->pivot->quantity ?? 1;

                                // Check if this material has already been deducted (OUT) for this SPK
                                $alreadyDeducted = \App\Models\MaterialTransaction::where('reference_type', 'WorkOrder')
                                    ->where('reference_id', $wo->id)
                                    ->where('material_id', $mat->id)
                                    ->where('type', 'OUT')
                                    ->exists();

                                if (!$alreadyDeducted) {
                                    $freshMat = \App\Models\Material::where('id', $mat->id)->lockForUpdate()->first();
                                    if ($freshMat) {
                                        $freshMat->decrement('stock', $qty);
                                        $materialService->logTransaction(
                                            $freshMat,
                                            'OUT',
                                            $qty,
                                            'WorkOrder',
                                            $wo->id,
                                            "Pemakaian fisik material oleh Produksi via Surat Jalan #{$suratJalan->nomor_surat}"
                                        );
                                    }
                                }
                            }
                        }
                    } elseif ($suratJalan->jenis_serah_terima === 'produksi_to_post_qc') {
                        if ($wo->status === \App\Enums\WorkOrderStatus::PRODUCTION) {
                            $workflowService->updateStatus($wo, \App\Enums\WorkOrderStatus::QC);
                        }
                    }
                }
            });

            $targetName = $suratJalan->jenis_serah_terima === 'sortir_to_produksi' ? 'Produksi' : 'QC';
            return redirect()->back()->with('success', "Surat Jalan {$suratJalan->nomor_surat} telah dikonfirmasi diterima. Seluruh SPK otomatis berpindah ke {$targetName} & data teknisi tercatat!");
        } catch (\Throwable $e) {
            Log::error("Surat Jalan Receive Error (#{$id}): " . $e->getMessage());
            return redirect()->back()->with('error', "Gagal menerima Surat Jalan: " . $e->getMessage());
        }
    }
}
