<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\SuratJalanItem;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SuratJalanController extends Controller
{
    /**
     * Display list of Surat Jalan (FR-10.1, SRS §3.5)
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['pengirim', 'penerima', 'items.workOrder']);

        if ($request->filled('jenis')) {
            $query->where('jenis_serah_terima', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suratJalanList = $query->latest()->paginate(15);

        return view('surat-jalan.index', compact('suratJalanList'));
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
            $availableOrders = WorkOrder::where('status', \App\Enums\WorkOrderStatus::PRODUCTION)
                ->whereHas('logs', function($lq) {
                    $lq->where('step', 'SORTIR')
                       ->where('action', 'CLASSIFICATION_COMPLETED');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'sortir_to_produksi');
                })->get();
        } elseif ($jenis === 'produksi_to_post_qc') {
            $availableOrders = WorkOrder::where('status', \App\Enums\WorkOrderStatus::PRODUCTION)
                ->where(function($q) {
                    $q->whereNotNull('prod_sol_completed_at')
                      ->orWhereNotNull('prod_upper_completed_at')
                      ->orWhereNotNull('prod_cleaning_completed_at');
                })
                ->whereDoesntHave('suratJalanItems.suratJalan', function($q) {
                    $q->where('jenis_serah_terima', 'produksi_to_post_qc');
                })->get();
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
        $suratJalan = SuratJalan::with(['pengirim', 'penerima', 'items.workOrder'])->findOrFail($id);

        return view('surat-jalan.show', compact('suratJalan'));
    }

    /**
     * Print preview / PDF view of Surat Jalan (FR-10.1)
     */
    public function print($id)
    {
        $suratJalan = SuratJalan::with(['pengirim', 'penerima', 'items.workOrder.services', 'items.workOrder.lead'])->findOrFail($id);

        return view('surat-jalan.print', compact('suratJalan'));
    }

    /**
     * Mark Surat Jalan as received by target stage Admin and transition SPKs
     */
    public function markAsReceived(Request $request, $id)
    {
        $suratJalan = SuratJalan::with('items.workOrder')->findOrFail($id);
        
        DB::transaction(function () use ($suratJalan) {
            $suratJalan->update([
                'penerima_id' => Auth::id() ?? 1,
                'diterima_at' => now(),
                'status' => 'DITERIMA',
            ]);

            $workflowService = app(\App\Services\WorkflowService::class);

            foreach ($suratJalan->items as $item) {
                if (!$item->workOrder) continue;
                $wo = $item->workOrder;

                if ($suratJalan->jenis_serah_terima === 'sortir_to_produksi') {
                    if ($wo->status === \App\Enums\WorkOrderStatus::SORTIR) {
                        $workflowService->updateStatus($wo, \App\Enums\WorkOrderStatus::PRODUCTION);
                    }
                } elseif ($suratJalan->jenis_serah_terima === 'produksi_to_post_qc') {
                    if ($wo->status === \App\Enums\WorkOrderStatus::PRODUCTION) {
                        $workflowService->updateStatus($wo, \App\Enums\WorkOrderStatus::QC);
                    }
                }
            }
        });

        return redirect()->back()->with('success', "Surat Jalan {$suratJalan->nomor_surat} telah dikonfirmasi diterima. Seluruh SPK otomatis berpindah status!");
    }
}
