<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Models\User;
use App\Enums\WorkOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkshopRndController extends Controller
{
    /**
     * Display R&D Dedicated Station Dashboard (3-Tab Architecture)
     */
    public function index(Request $request)
    {
        $selectedStage = $request->get('stage', 'all'); // 'all', 'PREPARATION', 'SORTIR', 'PRODUCTION', 'QC'
        $search = $request->get('search');

        // Status Categories
        $prePrepStatuses = [
            WorkOrderStatus::SPK_PENDING,
            WorkOrderStatus::DITERIMA,
            WorkOrderStatus::ASSESSMENT,
            WorkOrderStatus::WAITING_PAYMENT,
            WorkOrderStatus::WAITING_VERIFICATION,
            WorkOrderStatus::READY_TO_DISPATCH,
            WorkOrderStatus::OTW_WORKSHOP,
        ];

        $activeWorkshopStatuses = [
            WorkOrderStatus::PREPARATION,
            WorkOrderStatus::SORTIR,
            WorkOrderStatus::PRODUCTION,
            WorkOrderStatus::QC,
        ];

        // Metric Counts
        $baseRnd = WorkOrder::onlyRnd();

        $metrics = [
            'countMonitoring' => (clone $baseRnd)->whereIn('status', $prePrepStatuses)->count(),
            'totalActive' => (clone $baseRnd)->whereIn('status', $activeWorkshopStatuses)->count(),
            'countPrep' => (clone $baseRnd)->where('status', WorkOrderStatus::PREPARATION)->count(),
            'countSortir' => (clone $baseRnd)->where('status', WorkOrderStatus::SORTIR)->count(),
            'countProd' => (clone $baseRnd)->where('status', WorkOrderStatus::PRODUCTION)->count(),
            'countQc' => (clone $baseRnd)->where('status', WorkOrderStatus::QC)->count(),
            'totalCompleted' => (clone $baseRnd)->where('status', WorkOrderStatus::SELESAI)->count(),
        ];

        // Intelligent Default Tab
        $tab = $request->get('tab');
        if (!$tab) {
            if ($metrics['totalActive'] > 0) {
                $tab = 'active';
            } elseif ($metrics['countMonitoring'] > 0) {
                $tab = 'monitoring';
            } else {
                $tab = 'active';
            }
        }

        // Queries for 3 Tabs
        $monitoringQuery = WorkOrder::onlyRnd()
            ->whereIn('status', $prePrepStatuses)
            ->with(['customer', 'workOrderServices.service', 'rndProgresses', 'logs', 'invoice']);

        $activeQuery = WorkOrder::onlyRnd()
            ->whereIn('status', $activeWorkshopStatuses)
            ->with(['customer', 'workOrderServices.service', 'rndProgresses', 'logs', 'technicianProduction', 'invoice']);

        $completedQuery = WorkOrder::onlyRnd()
            ->where('status', WorkOrderStatus::SELESAI)
            ->with(['customer', 'workOrderServices.service', 'rndProgresses', 'logs', 'technicianProduction', 'invoice']);

        // Apply Stage Filter on Active Query
        if ($selectedStage !== 'all') {
            $activeQuery->where('status', $selectedStage);
        }

        // Apply Search Filter across all queries
        if ($search) {
            $searchFilter = function ($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('shoe_brand', 'like', "%{$search}%")
                  ->orWhere('shoe_type', 'like', "%{$search}%");
            };
            $monitoringQuery->where($searchFilter);
            $activeQuery->where($searchFilter);
            $completedQuery->where($searchFilter);
        }

        // Pagination
        $monitoringOrders = $monitoringQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'monitoring_page');
        $activeOrders = $activeQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'active_page');
        $completedOrders = $completedQuery->orderBy('finished_date', 'desc')->paginate(15, ['*'], 'completed_page');

        // Active Technicians categorized by station & specialization
        $allActiveTechs = User::where('is_active', true)
            ->whereIn('role', ['technician', 'admin'])
            ->select('id', 'name', 'role', 'station', 'specialization')
            ->orderBy('name')
            ->get();

        $stageTechnicians = [
            'PREPARATION' => $allActiveTechs->filter(function($u) {
                return $u->station === 'PREPARATION' 
                    || in_array($u->specialization, ['Washing', 'Bongkar Sol', 'Cuci']);
            })->values(),

            'PRODUCTION' => [
                'Soling / Sol' => $allActiveTechs->filter(function($u) {
                    return $u->station === 'SOLING' 
                        || in_array($u->specialization, ['Reparasi Sol', 'Sol Repair', 'PIC Material Sol']);
                })->values(),

                'Upper / Jahit' => $allActiveTechs->filter(function($u) {
                    return $u->station === 'UPPER' 
                        || in_array($u->specialization, ['Reparasi Upper', 'Upper Repair', 'PIC Material Upper', 'Jahit']);
                })->values(),

                'Treatment / Repaint' => $allActiveTechs->filter(function($u) {
                    return $u->station === 'TREATMENT' 
                        || in_array($u->specialization, ['Reparasi Treatment', 'Treatment', 'Repaint', 'Clean Up']);
                })->values(),
            ],

            'QC' => $allActiveTechs->filter(function($u) {
                return $u->station === 'QC' 
                    || in_array($u->specialization, ['QC Final', 'QC Jahit', 'QC Cleanup', 'PIC QC']);
            })->values(),
        ];

        // Active Sub-Stations & Qualified Technicians Map
        $rndSubStations = [
            'PREPARATION' => [
                'prep_washing' => [
                    'label' => 'Cuci (Washing)',
                    'short_label' => 'Cuci',
                    'col' => 'prep_washing_by',
                    'techs' => $allActiveTechs->filter(fn($u) => in_array($u->specialization, ['Washing', 'Cuci']) || $u->station === 'PREPARATION')->values(),
                ],
                'prep_sol' => [
                    'label' => 'Bongkar Sol',
                    'short_label' => 'Bongkar Sol',
                    'col' => 'prep_sol_by',
                    'techs' => $allActiveTechs->filter(fn($u) => in_array($u->specialization, ['Bongkar Sol']) || $u->station === 'PREPARATION' || $u->station === 'SOLING')->values(),
                ],
                'prep_upper' => [
                    'label' => 'Prep Upper',
                    'short_label' => 'Prep Upper',
                    'col' => 'prep_upper_by',
                    'techs' => $allActiveTechs->filter(fn($u) => $u->station === 'UPPER' || in_array($u->specialization, ['Reparasi Upper', 'PIC Material Upper', 'Jahit']))->values(),
                ],
            ],
            'PRODUCTION' => [
                'prod_sol' => [
                    'label' => 'Reparasi Soling',
                    'short_label' => 'Soling',
                    'col' => 'prod_sol_by',
                    'techs' => $allActiveTechs->filter(fn($u) => $u->station === 'SOLING' || in_array($u->specialization, ['Reparasi Sol', 'Sol Repair', 'PIC Material Sol']))->values(),
                ],
                'prod_upper' => [
                    'label' => 'Reparasi Upper',
                    'short_label' => 'Upper',
                    'col' => 'prod_upper_by',
                    'techs' => $allActiveTechs->filter(fn($u) => $u->station === 'UPPER' || in_array($u->specialization, ['Reparasi Upper', 'Upper Repair', 'Jahit']))->values(),
                ],
                'qc_jahit' => [
                    'label' => 'QC Jahit',
                    'short_label' => 'QC Jahit',
                    'col' => 'qc_jahit_by',
                    'techs' => $allActiveTechs->filter(fn($u) => in_array($u->specialization, ['QC Jahit', 'Jahit']) || $u->station === 'QC' || $u->station === 'UPPER')->values(),
                ],
            ],
            'QC' => [
                'prod_cleaning' => [
                    'label' => 'Treatment / Repaint',
                    'short_label' => 'Treatment',
                    'col' => 'prod_cleaning_by',
                    'techs' => $allActiveTechs->filter(fn($u) => $u->station === 'TREATMENT' || in_array($u->specialization, ['Reparasi Treatment', 'Treatment', 'Repaint', 'Clean Up']))->values(),
                ],
                'qc_cleanup' => [
                    'label' => 'QC Cleanup',
                    'short_label' => 'QC Cleanup',
                    'col' => 'qc_cleanup_by',
                    'techs' => $allActiveTechs->filter(fn($u) => in_array($u->specialization, ['QC Cleanup', 'Clean Up']) || $u->station === 'QC')->values(),
                ],
                'qc_final' => [
                    'label' => 'QC Final',
                    'short_label' => 'QC Final',
                    'col' => 'qc_final_by',
                    'techs' => $allActiveTechs->filter(fn($u) => in_array($u->specialization, ['QC Final', 'PIC QC']) || $u->station === 'QC')->values(),
                ],
            ],
        ];

        $technicians = $allActiveTechs;
        $layout = 'workshop-pwa-layout';

        return view('workshop.rnd.index', compact(
            'monitoringOrders',
            'activeOrders',
            'completedOrders',
            'metrics',
            'tab',
            'selectedStage',
            'search',
            'technicians',
            'stageTechnicians',
            'rndSubStations',
            'layout'
        ));
    }

    /**
     * Start Research: Fast-track pre-prep SPK into PREPARATION
     */
    public function startResearch(Request $request, $id)
    {
        $order = WorkOrder::onlyRnd()->findOrFail($id);
        $prevStatus = $order->status instanceof WorkOrderStatus ? $order->status->value : (string)$order->status;

        DB::transaction(function () use ($order, $prevStatus) {
            $order->status = WorkOrderStatus::PREPARATION;
            $order->save();

            // Auto-release from Inbound / Before Rack
            try {
                app(\App\Services\Storage\StorageService::class)->releaseFromInbound($order);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Rnd startResearch: Failed to release from inbound: " . $e->getMessage());
            }

            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => Auth::id() ?? 1,
                'step' => 'PREPARATION',
                'action' => 'RND_RESEARCH_STARTED',
                'description' => "Proyek Riset R&D resmi dimulai dari status {$prevStatus} ke PREPARATION oleh " . (Auth::user()?->name ?? 'Admin Workshop'),
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Proyek riset SPK #{$order->spk_number} resmi dimulai di tahap PREPARATION.",
            ]);
        }

        return redirect()->route('workshop.rnd.index', ['tab' => 'active'])
            ->with('success', "Proyek riset SPK #{$order->spk_number} resmi dimulai di tahap PREPARATION.");
    }

    /**
     * Update Stage in Interactive Stepper
     */
    public function updateStage(Request $request, $id)
    {
        $request->validate([
            'stage' => 'required|in:PREPARATION,SORTIR,PRODUCTION,QC',
        ]);

        $order = WorkOrder::onlyRnd()->findOrFail($id);
        $prevStatus = $order->status instanceof WorkOrderStatus ? $order->status->value : (string)$order->status;
        $newStage = $request->stage;

        DB::transaction(function () use ($order, $prevStatus, $newStage) {
            $now = now();
            $order->status = WorkOrderStatus::from($newStage);

            // Auto-complete previous stage technicians if moving forward
            $stageOrder = ['PREPARATION' => 1, 'SORTIR' => 2, 'PRODUCTION' => 3, 'QC' => 4];
            $prevIndex = $stageOrder[$prevStatus] ?? 0;
            $newIndex = $stageOrder[$newStage] ?? 0;

            if ($newIndex > $prevIndex) {
                $stationsToComplete = [];
                if ($prevStatus === 'PREPARATION' || $newIndex >= 2) {
                    $stationsToComplete = array_merge($stationsToComplete, ['prep_washing', 'prep_sol', 'prep_upper']);
                }
                if ($prevStatus === 'PRODUCTION' || $newIndex >= 4) {
                    $stationsToComplete = array_merge($stationsToComplete, ['prod_sol', 'prod_upper', 'qc_jahit']);
                }

                foreach (array_unique($stationsToComplete) as $st) {
                    $byCol = "{$st}_by";
                    $startedCol = "{$st}_started_at";
                    $completedCol = "{$st}_completed_at";

                    if (!empty($order->{$byCol}) && is_null($order->{$completedCol})) {
                        $order->{$startedCol} = $order->{$startedCol} ?: $now;
                        $order->{$completedCol} = $now;
                    }
                }
            }

            // Auto-release from Inbound / Before Rack if still stored
            try {
                app(\App\Services\Storage\StorageService::class)->releaseFromInbound($order);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Rnd updateStage: Failed to release from inbound: " . $e->getMessage());
            }

            $order->save();

            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => Auth::id() ?? 1,
                'step' => $newStage,
                'action' => 'RND_STAGE_CHANGED',
                'description' => "Tahapan Riset diubah dari {$prevStatus} ke {$newStage} oleh " . (Auth::user()?->name ?? 'Admin Workshop'),
            ]);
        });

        // Determine current PIC for the new stage
        $currentPicId = match($newStage) {
            'PREPARATION' => $order->prep_washing_by ?? $order->prep_sol_by ?? $order->prep_upper_by,
            'PRODUCTION' => $order->technician_production_id ?? $order->prod_sol_by ?? $order->prod_upper_by ?? $order->qc_jahit_by,
            'QC' => $order->qc_final_by ?? $order->qc_final_pic_id ?? $order->prod_cleaning_by ?? $order->qc_cleanup_by,
            default => null,
        };
        $currentPicName = $currentPicId ? User::find($currentPicId)?->name : null;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Tahapan SPK #{$order->spk_number} berhasil dipindahkan ke {$newStage}.",
                'current_stage' => $newStage,
                'pic_id' => $currentPicId,
                'pic_name' => $currentPicName,
            ]);
        }

        return back()->with('success', "Tahapan SPK #{$order->spk_number} berhasil dipindahkan ke {$newStage}.");
    }

    /**
     * Inline Assign Technician PIC & Sub-Station for R&D Project
     */
    public function assignTechnician(Request $request, $id)
    {
        $request->validate([
            'sub_station' => 'nullable|string',
            'technician_id' => 'nullable|string', // numeric user ID, 'none', or empty string
            'stage' => 'nullable|string',
        ]);

        $order = WorkOrder::onlyRnd()->findOrFail($id);
        $subStation = $request->input('sub_station');
        $techId = $request->input('technician_id');
        $stage = $request->input('stage') ?? ($order->status instanceof WorkOrderStatus ? $order->status->value : (string)$order->status);

        DB::transaction(function () use ($order, $subStation, $techId, $stage) {
            if ($subStation) {
                // Sub-station specific handling (e.g. prep_washing, prod_sol, qc_final)
                if ($techId === 'none') {
                    $order->markStationUnneeded($subStation, Auth::id());
                } else {
                    $targetTechId = (!empty($techId) && is_numeric($techId)) ? (int)$techId : null;

                    $oldTechId = $order->{"{$subStation}_by"};
                    if ($order->isStationUnneeded($subStation)) {
                        $order->restoreStationNeeded($subStation, $targetTechId, Auth::id());
                    } else {
                        $order->{"{$subStation}_by"} = $targetTechId;
                    }

                    if ($targetTechId) {
                        if (empty($order->{"{$subStation}_started_at"})) {
                            $order->{"{$subStation}_started_at"} = now();
                        }
                        if (in_array($subStation, ['prod_sol', 'prod_upper', 'qc_jahit'])) {
                            $order->technician_production_id = $targetTechId;
                        }
                    }
                    $order->save();

                    // Log assignment if technician changed and wasn't handled by restoreStationNeeded
                    if (!$order->isStationUnneeded($subStation) && $oldTechId != $targetTechId) {
                        $oldName = $oldTechId ? (User::find($oldTechId)?->name ?? "ID $oldTechId") : 'Kosong';
                        $newName = $targetTechId ? (User::find($targetTechId)?->name ?? "ID $targetTechId") : 'Kosong';
                        $stationLabels = [
                            'prep_washing' => 'Cuci (Washing)',
                            'prep_sol'     => 'Bongkar Sol',
                            'prep_upper'   => 'Prep Upper',
                            'prod_sol'     => 'Reparasi Soling',
                            'prod_upper'   => 'Reparasi Upper',
                            'qc_jahit'     => 'QC Jahit',
                            'prod_cleaning'=> 'Treatment / Repaint',
                            'qc_cleanup'   => 'QC Cleanup',
                            'qc_final'     => 'QC Final',
                        ];
                        $label = $stationLabels[$subStation] ?? ucwords(str_replace('_', ' ', $subStation));
                        $logStep = in_array($subStation, ['prod_cleaning', 'qc_cleanup', 'qc_final']) ? 'QC' : (str_starts_with($subStation, 'prep_') ? 'PREPARATION' : 'PRODUCTION');

                        WorkOrderLog::create([
                            'work_order_id' => $order->id,
                            'user_id'       => Auth::id() ?? 1,
                            'step'          => $logStep,
                            'action'        => 'RND_TECH_ASSIGNED',
                            'description'   => "Teknisi Sub-stasiun {$label} ditugaskan: [{$oldName} ➔ {$newName}] oleh " . (Auth::user()?->name ?? 'Admin Workshop'),
                        ]);
                    }
                }
            } else {
                // Stage-wide fallback assignment
                $targetTechId = (!empty($techId) && is_numeric($techId)) ? (int)$techId : null;

                if ($stage === 'PREPARATION') {
                    $order->prep_washing_by = $targetTechId;
                } elseif ($stage === 'PRODUCTION') {
                    $order->technician_production_id = $targetTechId;
                    $order->prod_sol_by = $targetTechId;
                } elseif ($stage === 'QC') {
                    $order->prod_cleaning_by = $targetTechId;
                    $order->qc_final_by = $targetTechId;
                }
                $order->save();
            }
        });

        $order->refresh();
        $isUnneeded = $subStation ? $order->isStationUnneeded($subStation) : false;
        $currentTechId = ($subStation && !$isUnneeded) ? $order->{"{$subStation}_by"} : null;
        $currentTechUser = $currentTechId ? User::find($currentTechId) : null;
        $currentTechName = $isUnneeded ? 'Tidak Diperlukan' : ($currentTechUser ? $currentTechUser->name : 'Belum Ditugaskan');

        // Main display PIC name for table row
        $summaryPicId = $order->technician_production_id 
            ?? $order->prep_washing_by 
            ?? $order->prep_sol_by 
            ?? $order->prep_upper_by 
            ?? $order->qc_final_by;
        $summaryPicName = $summaryPicId ? User::find($summaryPicId)?->name : 'Belum Ditugaskan';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Penugasan sub-proses berhasil disimpan.",
                'sub_station' => $subStation,
                'is_unneeded' => $isUnneeded,
                'technician_id' => $isUnneeded ? 'none' : ($currentTechId ?? ''),
                'technician_name' => $currentTechName,
                'summary_pic' => $summaryPicName,
                'stage' => $stage,
            ]);
        }

        return back()->with('success', "Penugasan sub-proses berhasil disimpan.");
    }

    /**
     * Finalize & Complete R&D Project
     */
    public function complete(Request $request, $id)
    {
        $order = WorkOrder::onlyRnd()->findOrFail($id);

        $currentStatusVal = $order->status instanceof WorkOrderStatus ? $order->status->value : (string)$order->status;
        if ($currentStatusVal !== 'QC') {
            $msg = "Proyek riset SPK #{$order->spk_number} belum bisa diselesaikan karena belum mencapai tahap akhir (QC). Tahap saat ini: {$currentStatusVal}.";
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return back()->with('error', $msg);
        }

        DB::transaction(function () use ($order) {
            $now = now();
            $order->status = WorkOrderStatus::SELESAI;
            $order->finished_date = $now;

            // Auto-complete all assigned sub-stations on finish
            $allSubStations = [
                'prep_washing', 'prep_sol', 'prep_upper',
                'prod_sol', 'prod_upper', 'prod_cleaning',
                'qc_jahit', 'qc_cleanup', 'qc_final'
            ];

            foreach ($allSubStations as $st) {
                $byCol = "{$st}_by";
                $startedCol = "{$st}_started_at";
                $completedCol = "{$st}_completed_at";

                if (!empty($order->{$byCol}) && is_null($order->{$completedCol})) {
                    $order->{$startedCol} = $order->{$startedCol} ?: $now;
                    $order->{$completedCol} = $now;
                }
            }

            // Ensure Inbound / Before Rack is auto-released if not done yet
            try {
                app(\App\Services\Storage\StorageService::class)->releaseFromInbound($order);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Rnd complete: Failed to release from inbound: " . $e->getMessage());
            }

            $order->save();

            WorkOrderLog::create([
                'work_order_id' => $order->id,
                'user_id' => Auth::id() ?? 1,
                'step' => 'FINISH',
                'action' => 'RND_RESEARCH_COMPLETED',
                'description' => "Proyek Riset R&D berhasil diselesaikan (Status SELESAI). Semua teknisi sub-stasiun yang ditugaskan otomatis dituntaskan oleh " . (Auth::user()?->name ?? 'Admin Workshop'),
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Riset SPK #{$order->spk_number} telah selesai dan dipindahkan ke Riwayat Selesai.",
            ]);
        }

        return redirect()->route('workshop.rnd.index', ['tab' => 'completed'])
            ->with('success', "Proyek Riset SPK #{$order->spk_number} telah berhasil diselesaikan.");
    }
}
