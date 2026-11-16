<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Data Divisi Workshop untuk Laporan MoM InsightSW (Workshop Tahap 1, additive).
 *
 * Pola sama dengan WarehouseMomQuery: angka periode dari riwayat, angka posisi dihitung per tanggal (as-of)
 * dengan rumus status-pada-T yang sama (WarehouseMomQuery::STATUS_AT_T_SQL). Hanya SELECT, kompatibel MySQL 5.5,
 * persentil di PHP. Definisi dan validasi: laporan_kerja/2026-11/IMPLEMENTASI_WORKSHOP_MOM_TAHAP1.md.
 */
class WorkshopMomQuery
{
    /** WIP bengkel (keputusan pemilik). */
    public const STATUS_BENGKEL = ['SORTIR', 'PREPARATION', 'PRODUCTION', 'QC', 'REVISI'];

    /** WIP versi Kamus = bengkel + status sebelum/di sekitar bengkel. */
    public const STATUS_KAMUS_TAMBAHAN = ['DITERIMA', 'ASSESSMENT', 'CX_FOLLOWUP', 'OTW_WORKSHOP'];

    /** Estimasi selesai SPK: estimasi baru (bila ada) atau estimasi awal. Nilai sekarang (riwayatnya tidak terstruktur). */
    private const ESTIMASI_SQL = 'COALESCE(w.new_estimation_date, DATE(w.estimation_date))';

    private const OTW_PERTAMA_SQL = "(SELECT MIN(l.created_at) FROM work_order_logs l
        WHERE l.work_order_id = w.id AND l.step = 'OTW_WORKSHOP' AND l.action = 'STATUS_CHANGE')";

    private const TIBA_SQL = "COALESCE((SELECT MIN(l.created_at) FROM work_order_logs l
        WHERE l.work_order_id = w.id AND l.step = 'DITERIMA' AND l.action = 'STATUS_CHANGE'), w.warehouse_qc_at)";

    public static function periode(Carbon $start, Carbon $end): array
    {
        $named = ['s' => $start->toDateTimeString(), 'e' => $end->toDateTimeString()];
        $gr = WarehouseMomQuery::gr('w');

        // 1. Output selesai (Kamus No. 54) beserta bahan lead time, OTC, dan carryover.
        $rows = WarehouseMomQuery::select("
            SELECT {$gr} AS is_gr, w.finished_date, w.entry_date, ".self::ESTIMASI_SQL.' AS estimasi,
                   '.self::OTW_PERTAMA_SQL.' AS otw_at, '.self::TIBA_SQL." AS tiba_at
            FROM work_orders w
            WHERE w.deleted_at IS NULL AND w.finished_date BETWEEN :s AND :e
              AND w.status NOT IN ('BATAL', 'CANCELLED')
        ", $named);

        $leadWorkshop = $leadKamus = [];
        $tanpaOtw = $garansi = 0;
        $otc = ['n_dengan_estimasi' => 0, 'tepat_waktu' => 0, 'terlambat' => 0, 'tanpa_estimasi' => 0];
        $carry = ['output_dari_tiba_bulan_ini' => 0, 'output_dari_tiba_sebelumnya' => 0, 'output_tanpa_tanggal_tiba' => 0];

        foreach ($rows as $r) {
            $selesai = Carbon::parse($r->finished_date);
            $garansi += (int) $r->is_gr;

            if ($r->otw_at === null) {
                $tanpaOtw++;
            } else {
                $leadWorkshop[] = max(0, Carbon::parse($r->otw_at)->diffInMinutes($selesai, false)) / 1440;
            }
            if ($r->entry_date !== null) {
                $leadKamus[] = max(0, Carbon::parse($r->entry_date)->diffInMinutes($selesai, false)) / 1440;
            }

            if ($r->estimasi === null) {
                $otc['tanpa_estimasi']++;
            } else {
                $otc['n_dengan_estimasi']++;
                $selesai->toDateString() <= substr((string) $r->estimasi, 0, 10) ? $otc['tepat_waktu']++ : $otc['terlambat']++;
            }

            if ($r->tiba_at === null) {
                $carry['output_tanpa_tanggal_tiba']++;
            } elseif (substr((string) $r->tiba_at, 0, 7) === $selesai->format('Y-m')) {
                $carry['output_dari_tiba_bulan_ini']++;
            } else {
                $carry['output_dari_tiba_sebelumnya']++;
            }
        }

        // 2. Batal (Kamus No. 63): waktu batal dari log BATAL terakhir (STATUS_CHANGE atau ORDER_CANCELLED), fallback updated_at.
        $batal = WarehouseMomQuery::select("
            SELECT COUNT(*) AS n, COALESCE(SUM(z.dari_log), 0) AS dari_log
            FROM (
                SELECT COALESCE(
                           (SELECT MAX(l.created_at) FROM work_order_logs l
                             WHERE l.work_order_id = w.id AND l.step IN ('BATAL', 'CANCELLED') AND l.action IN ('STATUS_CHANGE', 'ORDER_CANCELLED')),
                           w.updated_at) AS batal_at,
                       EXISTS (SELECT 1 FROM work_order_logs l
                             WHERE l.work_order_id = w.id AND l.step IN ('BATAL', 'CANCELLED') AND l.action IN ('STATUS_CHANGE', 'ORDER_CANCELLED')) AS dari_log
                FROM work_orders w
                WHERE w.deleted_at IS NULL AND w.status IN ('BATAL', 'CANCELLED')
            ) z
            WHERE z.batal_at BETWEEN :s AND :e
        ", $named)[0];

        // 3. SPK dibuat di periode: penyebut batal (non-GR) dan fast track (Kamus No. 62, tanpa BATAL).
        $dibuat = WarehouseMomQuery::select("
            SELECT
                COALESCE(SUM(NOT {$gr}), 0) AS non_gr,
                COALESCE(SUM(w.status NOT IN ('BATAL', 'CANCELLED')), 0) AS tanpa_batal,
                COALESCE(SUM(w.status NOT IN ('BATAL', 'CANCELLED') AND w.fast_track_status = 'yes'), 0) AS ft_yes,
                COALESCE(SUM(w.status NOT IN ('BATAL', 'CANCELLED') AND w.fast_track_status = 'no'), 0) AS ft_no
            FROM work_orders w
            WHERE w.deleted_at IS NULL AND w.created_at BETWEEN :s AND :e
        ", $named)[0];

        return [
            'output_selesai' => count($rows),
            'output_garansi' => $garansi,
            'hari_kerja' => self::hariKerjaPeriode($start, $end),
            'lead_time_workshop' => ['n' => count($leadWorkshop), 'tanpa_log_otw' => $tanpaOtw] + self::ringkasLeadTime($leadWorkshop),
            'lead_time_kamus' => ['n' => count($leadKamus)] + self::ringkasLeadTime($leadKamus),
            'otc' => $otc + ['metode_estimasi' => 'nilai_sekarang'],
            'batal' => [
                'spk_batal' => (int) $batal->n,
                'spk_dibuat_non_gr' => (int) $dibuat->non_gr,
                'spk_batal_waktu_dari_log' => (int) $batal->dari_log,
                'metode_waktu_batal' => 'log_batal_fallback_updated_at',
            ],
            'fast_track' => [
                'spk_dibuat' => (int) $dibuat->tanpa_batal,
                'yes' => (int) $dibuat->ft_yes,
                'no' => (int) $dibuat->ft_no,
                'lainnya' => (int) $dibuat->tanpa_batal - (int) $dibuat->ft_yes - (int) $dibuat->ft_no,
            ],
            'carryover' => $carry,
        ];
    }

    /**
     * Posisi WIP Workshop pada $asOf. Status pada T memakai rumus Gudang Tahap 2.
     */
    public static function posisi(Carbon $asOf): array
    {
        $t = $asOf->toDateTimeString();
        $logWhere = WarehouseMomQuery::STATUS_LOG_WHERE;
        $logStep = WarehouseMomQuery::STATUS_LOG_STEP;
        $bengkel = "'".implode("','", self::STATUS_BENGKEL)."'";
        $kamus = "'".implode("','", array_merge(self::STATUS_BENGKEL, self::STATUS_KAMUS_TAMBAHAN))."'";

        $rows = WarehouseMomQuery::select("
            SELECT x.st, x.est_null, x.est_lewat, x.umur_menit, COUNT(*) AS n
            FROM (
                SELECT s.st,
                    s.estimasi IS NULL AS est_null,
                    (s.estimasi IS NOT NULL AND s.estimasi < DATE(:t)) AS est_lewat,
                    -- Umur WIP bengkel sejak dikirim ke workshop (log OTW_WORKSHOP pertama <= T).
                    CASE WHEN s.st IN ({$bengkel}) THEN
                        TIMESTAMPDIFF(MINUTE,
                            (SELECT MIN(l.created_at) FROM work_order_logs l
                              WHERE l.work_order_id = s.id AND l.step = 'OTW_WORKSHOP' AND l.action = 'STATUS_CHANGE'
                                AND l.created_at <= :t), :t)
                    END AS umur_menit
                FROM (
                    SELECT y.id, ".WarehouseMomQuery::STATUS_AT_T_SQL.' AS st, y.estimasi
                    FROM (
                        SELECT b.*,
                            SUBSTRING(b.log_x, 32) AS log_step,
                            CAST(LEFT(b.log_x, 19) AS DATETIME) AS log_at
                        FROM (
                            SELECT w.id, w.status, w.spk_number, w.finished_date, w.updated_at, w.donated_at,
                                '.self::ESTIMASI_SQL." AS estimasi,
                                (SELECT MAX(CONCAT(DATE_FORMAT(l.created_at, '%Y-%m-%d %H:%i:%s'), LPAD(l.id, 12, '0'), {$logStep}))
                                  FROM work_order_logs l
                                  WHERE l.work_order_id = w.id AND {$logWhere} AND l.created_at <= :t) AS log_x,
                                EXISTS (SELECT 1 FROM work_order_logs l
                                  WHERE l.work_order_id = w.id AND {$logWhere}) AS has_any_log
                            FROM work_orders w
                            WHERE w.created_at <= :t AND (w.deleted_at IS NULL OR w.deleted_at > :t)
                              AND NOT (w.status IN ('BATAL', 'DONASI', 'HISTORY') AND COALESCE(w.donated_at, w.updated_at) <= :t)
                        ) b
                    ) y
                ) s
                WHERE s.st IN ({$kamus})
            ) x
            GROUP BY x.st, x.est_null, x.est_lewat, x.umur_menit
        ", ['t' => $t]);

        $wip = array_fill_keys(self::STATUS_BENGKEL, 0);
        $kamusTotal = $overdue = $tanpaEstimasi = $tanpaOtw = 0;
        $umur = [];

        foreach ($rows as $r) {
            $n = (int) $r->n;
            $kamusTotal += $n;
            if (! array_key_exists($r->st, $wip)) {
                continue;
            }
            $wip[$r->st] += $n;
            if ($r->est_null) {
                $tanpaEstimasi += $n;
            } elseif ($r->est_lewat) {
                $overdue += $n;
            }
            if ($r->umur_menit === null) {
                $tanpaOtw += $n;
            } else {
                for ($i = 0; $i < $n; $i++) {
                    $umur[] = max(0, (int) $r->umur_menit) / 1440;
                }
            }
        }

        return [
            'as_of' => $t,
            'wip_bengkel' => $wip + ['total' => array_sum($wip)],
            'wip_kamus_total' => $kamusTotal,
            'overdue' => [
                'wip_bengkel_overdue' => $overdue,
                'wip_bengkel_tanpa_estimasi' => $tanpaEstimasi,
                'metode' => 'pendekatan',
            ],
            'umur_wip_bengkel' => [
                'median_hari' => WarehouseMomQuery::percentile($umur, 0.5),
                'gt_14_hari' => count(array_filter($umur, fn ($h) => $h > 14)),
                'gt_30_hari' => count(array_filter($umur, fn ($h) => $h > 30)),
                'n' => count($umur),
                'tanpa_log_otw' => $tanpaOtw,
            ],
            'metode' => [
                'wip_bengkel' => 'pendekatan',
                'wip_kamus_total' => 'pendekatan',
                'overdue' => 'pendekatan',
                'umur_wip_bengkel' => 'tanpa_log_otw_tidak_dihitung',
            ],
        ];
    }

    /** n sudah diisi pemanggil; berisi rata-rata, median, P80/P90, dan sebaran ≤14 / 15–30 / >30 hari. */
    private static function ringkasLeadTime(array $hari): array
    {
        return [
            'avg_hari' => $hari ? round(array_sum($hari) / count($hari), 1) : 0.0,
            'median_hari' => WarehouseMomQuery::percentile($hari, 0.5),
            'p80_hari' => WarehouseMomQuery::percentile($hari, 0.8),
            'p90_hari' => WarehouseMomQuery::percentile($hari, 0.9),
            'le_14_hari' => count(array_filter($hari, fn ($h) => $h <= 14)),
            'd15_30_hari' => count(array_filter($hari, fn ($h) => $h > 14 && $h <= 30)),
            'gt_30_hari' => count(array_filter($hari, fn ($h) => $h > 30)),
        ];
    }

    /** Jumlah hari di [$start, $end] tanpa hari Minggu (aturan Invoice::addWorkingDays). */
    public static function hariKerjaPeriode(Carbon $start, Carbon $end): int
    {
        $n = 0;
        for ($d = $start->copy()->startOfDay(); $d->lte($end); $d->addDay()) {
            if (! $d->isSunday()) {
                $n++;
            }
        }

        return $n;
    }
}
