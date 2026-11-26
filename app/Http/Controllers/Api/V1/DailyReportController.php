<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DailyReportQuery;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * GET /api/v1/daily-report?date=Y-m-d — Report Harian per divisi untuk InsightSW (read-only).
 * Tanpa date → hari ini. Bila satu blok gagal dihitung, blok itu dihilangkan dan galatnya di-log.
 */
class DailyReportController extends Controller
{
    private const BLOK = [
        'cs' => 'cs',
        'oto' => 'oto',
        'tambah_jasa' => 'tambahJasa',
        'qc_after' => 'qcAfter',
        'pendukung' => 'pendukung',
        'gudang' => 'gudang',
        // Tahap 6: blok CC
        'followup' => 'followup',
        'garansi' => 'garansi',
        'komplain' => 'komplain',
    ];

    public function index(Request $request): JsonResponse
    {
        $date = $this->parseDate($request->query('date'));
        if ($date === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter date harus berformat Y-m-d (WIB) dan tidak boleh melewati hari ini.',
            ], 422);
        }

        $data = [
            'tanggal' => $date->toDateString(),
            'dibuat_pada' => now()->toDateTimeString(),
        ];

        foreach (self::BLOK as $key => $method) {
            try {
                $data[$key] = DailyReportQuery::$method($date);
            } catch (\Throwable $e) {
                Log::error("daily-report: blok {$key} gagal dihitung", [
                    'tanggal' => $date->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /** Y-m-d yang valid dan tidak di masa depan; null bila tidak valid. Tanpa nilai → hari ini. */
    private function parseDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return now()->startOfDay();
        }
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }
        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
        if ($date->format('Y-m-d') !== $value || $date->gt(now()->startOfDay())) {
            return null; // mis. 2026-02-30, atau tanggal yang belum terjadi
        }

        return $date;
    }
}
