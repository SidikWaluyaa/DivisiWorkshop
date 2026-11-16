<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\WarehouseDashboardApiService;
use App\Services\WarehouseMomQuery;
use App\Http\Resources\V1\WarehouseDashboardSummaryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class WarehouseDashboardApiController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseDashboardApiService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    /**
     * Get warehouse dashboard summary
     *
     * @param Request $request
     * @return WarehouseDashboardSummaryResource
     */
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(7)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();
        $search = $request->search;

        // Kamus MoM (Tahap 2): blok tambahan hanya bila as_of_date dikirim. Divalidasi sebelum query lama berjalan.
        $asOf = null;
        if ($request->filled('as_of_date')) {
            $asOf = $this->parseAsOfDate((string) $request->as_of_date);
            if ($asOf === null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'as_of_date harus berformat Y-m-d dan tidak boleh melewati hari ini.',
                ], 422);
            }
        }

        $summaryData = $this->warehouseService->getWarehouseSummary($startDate, $endDate, $search);

        if ($asOf !== null) {
            $summaryData['mom'] = $this->momBlocks($startDate, $endDate, $asOf);
        }

        return new WarehouseDashboardSummaryResource($summaryData);
    }

    /** as_of_date (Y-m-d, WIB) → 23:59:59 hari itu; hari ini → saat ini. Null bila tidak valid. */
    private function parseAsOfDate(string $value): ?Carbon
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable $e) {
            return null;
        }
        if ($date->format('Y-m-d') !== $value) {
            return null; // mis. 2026-02-30
        }
        if ($date->isSameDay(now())) {
            return now();
        }

        return $date->lt(now()->startOfDay()) ? $date->endOfDay() : null;
    }

    /**
     * Blok MoM, di-cache: 24 jam bila as_of dan end_date sudah lewat, 10 menit bila menyentuh hari ini.
     * Kunci cache memuat semua parameter yang memengaruhi hasil.
     */
    private function momBlocks(Carbon $start, Carbon $end, Carbon $asOf): array
    {
        $today = now()->startOfDay();
        $isPast = $asOf->lt($today) && $end->lt($today);
        // v2: posisi_as_of.ready_for_pickup_sepatu (Tahap 2b)
        // v3: transit_periode, aging_rak_selesai, piutang_rak_selesai, aging_selesai_ke_keluar (Tahap 3)
        $key = 'warehouse_mom:v3:' . md5(implode('|', [
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            $asOf->toDateString(),
        ]));

        return Cache::remember($key, $isPast ? 86400 : 600, fn () => [
            'posisi_as_of' => WarehouseMomQuery::posisiAsOf($asOf),
            'sepatu_keluar_detail' => WarehouseMomQuery::sepatuKeluarDetail($start, $end),
            'kedatangan' => WarehouseMomQuery::kedatangan($start, $end, $asOf),
            'efisiensi_v2' => WarehouseMomQuery::efisiensiV2($start, $end),
            'pengiriman_workshop' => WarehouseMomQuery::pengirimanWorkshop($start, $end),
            'transit_periode' => WarehouseMomQuery::transitPeriode($start, $end),
        ]);
    }
}
