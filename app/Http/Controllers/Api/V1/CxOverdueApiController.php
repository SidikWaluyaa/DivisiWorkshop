<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CxOverdueApiController extends Controller
{
    // SLA Definitions (in Days)
    public const STAGE_SLAS = [
        'PREPARATION' => 1,
        'SORTIR' => 1,
        'PRODUCTION' => 3,
        'QC' => 1,
        'REVISI' => 2,
        'SELESAI' => 2,
        'DIANTAR' => 1,
    ];

    public function index(Request $request)
    {
        $today = Carbon::now();

        // 1. Calculate Scoreboard Statistics
        $scoreboard = $this->calculateScoreboard($today);

        // 2. Build Filtered Query for Overdue Table
        $query = WorkOrder::query()
            ->whereNotIn('status', [WorkOrderStatus::BATAL->value, WorkOrderStatus::DONASI->value]);

        // Filter: Active card / Stage filter
        if ($request->filled('stage')) {
            $stage = strtoupper($request->stage);
            if ($stage === 'GLOBAL') {
                $query->whereNotIn('status', [WorkOrderStatus::SELESAI->value, WorkOrderStatus::DIANTAR->value])
                    ->where('estimation_date', '<', $today);
            } else {
                $query->where('status', $stage);
            }
        }

        // Filter: Search Box (SPK Number or Customer Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('spk_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Filter: Customer Name specifically
        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', "%" . $request->customer_name . "%");
        }

        // Filter: Date Range (based on entry_date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('entry_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Get Paginated Data
        $perPage = $request->input('per_page', 25);
        $paginatedOrders = $query->paginate($perPage);

        // Map and Format Output Rows
        $formattedOrders = collect($paginatedOrders->items())->map(function($wo) use ($today) {
            $stage = $wo->status->value;
            $entryDate = $wo->waktu ?: $wo->updated_at;
            
            // Calculate Overdue Days
            $daysOverdue = 0;
            if ($stage === WorkOrderStatus::SELESAI->value || $stage === WorkOrderStatus::DIANTAR->value) {
                // Completed but on hold or in transit
                $sla = self::STAGE_SLAS[$stage] ?? 0;
                $daysOverdue = max(0, $today->diffInDays(Carbon::parse($entryDate)) - $sla);
            } elseif ($wo->estimation_date && $wo->estimation_date->lessThan($today)) {
                // Not completed and past global estimation
                $daysOverdue = max(0, $today->diffInDays(Carbon::parse($wo->estimation_date)));
            } elseif (isset(self::STAGE_SLAS[$stage])) {
                // In production stage, check stage-specific SLA
                $sla = self::STAGE_SLAS[$stage];
                $daysOverdue = max(0, $today->diffInDays(Carbon::parse($entryDate)) - $sla);
            }

            return [
                'id' => $wo->id,
                'spk_number' => $wo->spk_number,
                'customer_name' => $wo->customer_name,
                'shoe_brand' => $wo->shoe_brand,
                'shoe_type' => $wo->shoe_type ?: '-',
                'current_stage' => $wo->status->label(),
                'stage_value' => $stage,
                'tgl_masuk_stage' => $entryDate ? Carbon::parse($entryDate)->toIso8601String() : null,
                'estimation_date' => $wo->estimation_date ? $wo->estimation_date->toIso8601String() : null,
                'days_overdue' => $daysOverdue,
                'late_description' => $wo->late_description ?: 'Tidak ada catatan hambatan.',
                'detail_url' => route('admin.orders.show', $wo->id)
            ];
        });

        // Optional Sorting on collection (default: days_overdue desc)
        $sortBy = $request->input('sort_by', 'days_overdue');
        $sortOrder = $request->input('sort_order', 'desc');

        if ($sortBy === 'days_overdue') {
            $formattedOrders = $sortOrder === 'desc' 
                ? $formattedOrders->sortByDesc('days_overdue')->values() 
                : $formattedOrders->sortBy('days_overdue')->values();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data overdue SLA berhasil ditarik.',
            'timestamp' => now()->toIso8601String(),
            'scoreboard' => $scoreboard,
            'orders' => $formattedOrders,
            'pagination' => [
                'total' => $paginatedOrders->total(),
                'per_page' => $paginatedOrders->perPage(),
                'current_page' => $paginatedOrders->currentPage(),
                'last_page' => $paginatedOrders->lastPage(),
                'from' => $paginatedOrders->firstItem(),
                'to' => $paginatedOrders->lastItem(),
            ]
        ]);
    }

    private function calculateScoreboard(Carbon $today): array
    {
        $stats = [];
        
        // 1. Global Overdue (Non-Selesai, Non-Diantar, past estimation_date)
        $globalWo = WorkOrder::whereNotIn('status', [WorkOrderStatus::BATAL->value, WorkOrderStatus::DONASI->value, WorkOrderStatus::SELESAI->value, WorkOrderStatus::DIANTAR->value])
            ->where('estimation_date', '<', $today)
            ->get();

        $globalLateDays = $globalWo->sum(function($wo) use ($today) {
            return max(0, $today->diffInDays($wo->estimation_date));
        });

        $stats['GLOBAL'] = [
            'label' => 'Estimasi Kelewat (Global)',
            'overdue_count' => $globalWo->count(),
            'total_days_overdue' => $globalLateDays,
            'color_theme' => 'amber',
            'sub_label' => 'Akumulasi Keterlambatan'
        ];

        // 2. Stage-Specific Overdue
        foreach (self::STAGE_SLAS as $stage => $sla) {
            $stageWo = WorkOrder::where('status', $stage)->get();
            $overdueCount = 0;
            $totalDaysOverdue = 0;

            foreach ($stageWo as $wo) {
                $entryDate = $wo->waktu ?: $wo->updated_at;
                $days = max(0, $today->diffInDays(Carbon::parse($entryDate)) - $sla);
                if ($days > 0) {
                    $overdueCount++;
                    $totalDaysOverdue += $days;
                }
            }

            $theme = 'orange';
            if ($stage === 'REVISI') {
                $theme = 'rose';
            } elseif ($stage === 'SELESAI' || $stage === 'DIANTAR') {
                $theme = 'teal';
            }

            $stats[$stage] = [
                'label' => $stage === 'SELESAI' ? 'Selesai (Hold)' : ( $stage === 'DIANTAR' ? 'Diantar' : ucfirst(strtolower($stage)) ),
                'overdue_count' => $overdueCount,
                'total_days_overdue' => $totalDaysOverdue,
                'color_theme' => $theme,
                'sub_label' => 'Akumulasi Keterlambatan'
            ];
        }

        return $stats;
    }
}
