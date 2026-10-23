<?php

namespace App\Http\Controllers;

use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use App\Models\WorkOrderRevision;
use App\Services\WorkflowService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RevisionController extends Controller
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Display a listing of active revisions.
     */
    public function index(Request $request)
    {
        $baseQuery = WorkOrderRevision::with(['workOrder', 'creator', 'resolver']);

        // Dynamic Filtering Logic
        if ($request->filled('q')) {
            $baseQuery->whereHas('workOrder', function($q) use ($request) {
                $q->where('spk_number', 'LIKE', "%{$request->q}%")
                  ->orWhere('customer_name', 'LIKE', "%{$request->q}%");
            });
        }

        if ($request->filled('start_date')) {
            $baseQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $baseQuery->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('origin')) {
            if ($request->origin === 'SELESAI') {
                $baseQuery->where(function($q) {
                    $q->whereNull('origin_status')->orWhere('origin_status', 'SELESAI');
                });
            } else {
                $baseQuery->where('origin_status', $request->origin);
            }
        }

        if ($request->filled('pic')) {
            $baseQuery->where('created_by', $request->pic);
        }

        if ($request->filled('brand')) {
            $baseQuery->whereHas('workOrder', function($q) use ($request) {
                $q->where('shoe_brand', $request->brand);
            });
        }

        // Split into Active and History with Pagination
        $active = (clone $baseQuery)->where('status', 'OPEN')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page_active')
            ->withQueryString();

        $history = (clone $baseQuery)->where('status', 'FINISHED')
            ->orderBy('finished_at', 'desc')
            ->paginate(10, ['*'], 'page_history')
            ->withQueryString();

        // Fetch Dropdown Options Dynamically
        $reporters = \App\Models\User::whereIn('id', WorkOrderRevision::pluck('created_by')->unique())->get(['id', 'name']);
        $brands = \App\Models\WorkOrder::whereIn('id', WorkOrderRevision::pluck('work_order_id')->unique())
            ->pluck('shoe_brand')
            ->unique()
            ->filter()
            ->values();

        // Count metrics for open revisions
        $qcCount = WorkOrderRevision::where('status', 'OPEN')->where('origin_status', 'QC')->count();
        $prodCount = WorkOrderRevision::where('status', 'OPEN')->where('origin_status', 'PRODUCTION')->count();
        $selesaiCount = WorkOrderRevision::where('status', 'OPEN')->where(function($q) {
            $q->whereNull('origin_status')->orWhere('origin_status', 'SELESAI');
        })->count();
        $totalActiveCount = WorkOrderRevision::where('status', 'OPEN')->count();

        return view('revision.index', compact(
            'active', 
            'history', 
            'reporters', 
            'brands',
            'qcCount',
            'prodCount',
            'selesaiCount',
            'totalActiveCount'
        ));
    }

    /**
     * Display the details of a specific revision.
     */
    public function show(WorkOrderRevision $revision)
    {
        $revision->load(['workOrder', 'creator']);
        return view('revision.show', compact('revision'));
    }

    /**
     * Submit a revision request for a finished SPK.
     */
    public function request(Request $request, WorkOrder $workOrder)
    {
        $request->validate([
            'description' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image',
        ]);

        try {
            DB::transaction(function () use ($request, $workOrder) {
                // 1. Move WorkOrder status to REVISI
                $this->workflowService->updateStatus(
                    $workOrder,
                    WorkOrderStatus::REVISI,
                    'Mengajukan revisi teknik: ' . $request->description
                );

                // 2. Handle Multiple Photo Uploads
                $photoPaths = [];
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $index => $photo) {
                        $filename = 'REV_REQ_' . $workOrder->spk_number . '_' . time() . '_' . $index;
                        $photoPaths[] = \App\Utils\ImageHelper::convertToJpg($photo, 'photos/qc_reject', $filename);
                    }
                }

                // 3. Create Revision Record
                WorkOrderRevision::create([
                    'work_order_id' => $workOrder->id,
                    'description' => $request->description,
                    'photo_path' => $photoPaths[0] ?? null, // First photo for compatibility
                    'photo_paths' => $photoPaths,           // All photos
                    'status' => 'OPEN',
                    'created_by' => auth()->id(),
                ]);
            });

            return redirect()->back()->with('success', 'Revisi berhasil diajukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengajukan revisi: ' . $e->getMessage());
        }
    }

    /**
     * Mark a revision as completed and return to SELESAI status.
     */
    public function complete(Request $request, WorkOrderRevision $revision)
    {
        try {
            DB::transaction(function () use ($revision) {
                $workOrder = $revision->workOrder;

                // 1. Update Revision Status
                $revision->update([
                    'status' => 'FINISHED',
                    'resolved_by' => auth()->id(),
                    'finished_at' => now(),
                ]);

                // 2. Move WorkOrder back to origin status (or default to SELESAI)
                $targetStatusValue = $revision->origin_status ?: 'SELESAI';
                $targetStatus = WorkOrderStatus::tryFrom($targetStatusValue) ?: WorkOrderStatus::SELESAI;

                $this->workflowService->updateStatus(
                    $workOrder,
                    $targetStatus,
                    'Revisi selesai dikerjakan. Kembali ke tahap ' . $targetStatus->label()
                );

                // Clear revision flags
                $workOrder->is_revising = false;
                $workOrder->previous_status = null;
                $workOrder->save();
            });

            return redirect()->route('revision.index')->with('success', 'Revisi telah diselesaikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyelesaikan revisi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified revision from storage.
     */
    public function destroy(WorkOrderRevision $revision)
    {
        // 1. Authorization Check (Only Creator or Admin/Manager with 'finish' access)
        if ($revision->created_by !== auth()->id() && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus revisi ini.');
        }

        try {
            DB::transaction(function () use ($revision) {
                // 2. If status is OPEN, return WorkOrder to SELESAI
                if ($revision->status === 'OPEN') {
                    $workOrder = $revision->workOrder;
                    $this->workflowService->updateStatus(
                        $workOrder,
                        WorkOrderStatus::SELESAI,
                        'Menghapus ajuan revisi (Data dibatalkan).'
                    );
                }

                // 3. Cleanup Photos from Storage
                if ($revision->photo_paths) {
                    foreach ($revision->photo_paths as $path) {
                        Storage::disk('public')->delete($path);
                    }
                } elseif ($revision->photo_path) {
                    Storage::disk('public')->delete($revision->photo_path);
                }

                // 4. Delete the Record
                $revision->delete();
            });

            return redirect()->route('revision.index')->with('success', 'Data revisi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus revisi: ' . $e->getMessage());
        }
    }

    /**
     * Update estimated workshop loss details for a revision.
     */
    public function updateLoss(Request $request, WorkOrderRevision $revision)
    {
        $request->validate([
            'loss_amount' => 'nullable|numeric|min:0',
            'loss_category' => 'nullable|string',
            'loss_description' => 'nullable|string',
            'responsible_party' => 'nullable|string',
        ]);

        try {
            $lossAmount = $request->input('loss_amount', 0);
            $revision->update([
                'loss_amount' => is_numeric($lossAmount) ? floatval($lossAmount) : 0,
                'loss_category' => $request->input('loss_category', 'REWORK_LABOR'),
                'loss_description' => $request->input('loss_description'),
                'responsible_party' => $request->input('responsible_party'),
            ]);

            return redirect()->back()->with('success', 'Data kerugian workshop berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data kerugian: ' . $e->getMessage());
        }
    }

    /**
     * Export Monthly Revision Report to PDF.
     */
    public function exportPdf(Request $request)
    {
        // 1. Determine Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $periodLabel = $startDate->translatedFormat('d F Y') . ' - ' . $endDate->translatedFormat('d F Y');
            $filePeriod = $startDate->format('Ymd') . '_' . $endDate->format('Ymd');
        } else {
            $month = (int) $request->input('month', now()->month);
            $year = (int) $request->input('year', now()->year);
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->startOfDay();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
            $periodLabel = $startDate->translatedFormat('F Y');
            $filePeriod = $startDate->format('Y_m');
        }

        // 2. Query SPK Masuk Periode Ini (Berdasarkan created_at)
        $itemsMasuk = WorkOrderRevision::with(['workOrder', 'creator', 'resolver'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Query SPK Diselesaikan Periode Ini (Berdasarkan finished_at)
        $itemsSelesai = WorkOrderRevision::with(['workOrder', 'creator', 'resolver'])
            ->where('status', 'FINISHED')
            ->whereBetween('finished_at', [$startDate, $endDate])
            ->orderBy('finished_at', 'asc')
            ->get();

        // 4. Hitung Metrik Eksekutif
        $totalMasuk = $itemsMasuk->count();
        $masukSelesai = $itemsMasuk->where('status', 'FINISHED')->count();
        $masukOpen = $itemsMasuk->where('status', 'OPEN')->count();
        $totalSelesai = $itemsSelesai->count();
        $activePending = WorkOrderRevision::where('status', 'OPEN')->count();
        
        $completionRate = $totalMasuk > 0 ? round(($masukSelesai / $totalMasuk) * 100, 1) : 0;

        $data = [
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'itemsMasuk' => $itemsMasuk,
            'itemsSelesai' => $itemsSelesai,
            'totalMasuk' => $totalMasuk,
            'masukSelesai' => $masukSelesai,
            'masukOpen' => $masukOpen,
            'totalSelesai' => $totalSelesai,
            'activePending' => $activePending,
            'completionRate' => $completionRate,
            'printedBy' => auth()->user() ? auth()->user()->name : 'Sistem Workshop',
            'printedAt' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ];

        $pdf = Pdf::loadView('reports.pdf.revision-monthly-report', $data)
            ->setPaper('a4', 'landscape')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream('Laporan_Revisi_' . $filePeriod . '.pdf');
    }
}
