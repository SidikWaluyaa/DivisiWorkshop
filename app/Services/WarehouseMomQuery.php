<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Data gudang untuk Laporan MoM InsightSW (Tahap 2, additive).
 *
 * Semua angka dihitung "per tanggal" (as-of) dari riwayat, sehingga bulan berjalan dan bulan lalu
 * dihitung dengan cara yang sama. Hanya SELECT, kompatibel MySQL 5.5 (tanpa window function/CTE/JSON).
 * Definisi dan validasinya: laporan_kerja/2026-11/INVESTIGASI_GUDANG_MOM_TAHAP1.md.
 *
 * Catatan data penting:
 * - storage_assignments.stored_at ber-ON UPDATE CURRENT_TIMESTAMP (ikut berubah saat baris di-update);
 *   waktu simpan asli = storage_assignments.created_at.
 * - work_orders.status_pembayaran tidak terawat; status lunas diambil dari level invoice.
 */
class WarehouseMomQuery
{
    private const GR_SQL = "(%s.spk_number LIKE '%%-GR' OR %s.spk_number LIKE '%%-GR-%%')";

    /** Sumber tiap field posisi yang bukan rekonstruksi riwayat penuh. */
    public const METODE_POSISI = [
        'shipping_pending' => 'pendekatan',
        'total_capacity' => 'pendekatan',
        'wip_tahap' => 'pendekatan',
        'inbound_inventory' => 'riwayat_assignment',
        'finish_inventory' => 'riwayat_assignment_dan_barang_manual',
        'stored_items' => 'riwayat_assignment_dan_barang_manual',
    ];

    /**
     * Ubah placeholder :nama menjadi ? positional. PDO (emulate prepares off) tidak mengizinkan
     * placeholder bernama yang sama dipakai berulang.
     */
    private static function select(string $sql, array $named): array
    {
        $bindings = [];
        $sql = preg_replace_callback('/:([a-z_][a-z0-9_]*)/i', function ($m) use ($named, &$bindings) {
            if (! array_key_exists($m[1], $named)) {
                return $m[0];
            }
            $bindings[] = $named[$m[1]];

            return '?';
        }, $sql);

        return DB::select($sql, $bindings);
    }

    private static function gr(string $alias): string
    {
        return sprintf(self::GR_SQL, $alias, $alias);
    }

    /**
     * Log yang menandai perpindahan status. Selain STATUS_CHANGE, QC penerimaan gudang (QC_PASSED → ASSESSMENT,
     * QC_REJECTED → CX_FOLLOWUP) dipakai karena log STATUS_CHANGE ASSESSMENT baru tercatat sejak 2 Jul 2026.
     */
    private const STATUS_LOG_WHERE = "(l.action = 'STATUS_CHANGE' OR l.action IN ('QC_PASSED', 'QC_REJECTED'))";

    private const STATUS_LOG_STEP = "CASE l.action WHEN 'QC_PASSED' THEN 'ASSESSMENT' WHEN 'QC_REJECTED' THEN 'CX_FOLLOWUP' ELSE l.step END";

    /**
     * Ekspresi status SPK pada :t (lihat Tahap 1 A.5). Kolom yang dibutuhkan dari subquery y:
     * status, spk_number, finished_date, updated_at, donated_at, log_step, log_at, has_any_log.
     */
    private const STATUS_AT_T_SQL = "CASE
            WHEN y.status IN ('BATAL','DONASI','HISTORY') AND (y.log_step IS NULL OR y.log_step <> y.status)
                 AND COALESCE(y.donated_at, y.updated_at) <= :t THEN y.status
            WHEN y.finished_date IS NOT NULL AND y.finished_date <= :t
                 AND (y.log_at IS NULL OR y.finished_date >= y.log_at) THEN 'SELESAI'
            WHEN y.log_step IS NOT NULL THEN y.log_step
            WHEN y.has_any_log = 0 AND (y.spk_number LIKE '%-GR' OR y.spk_number LIKE '%-GR-%') THEN 'SELESAI'
            WHEN y.has_any_log = 0 AND y.status = 'BATAL' AND y.updated_at <= :t THEN 'BATAL'
            ELSE 'SPK_PENDING'
        END";

    /**
     * Posisi gudang pada $asOf.
     */
    public static function posisiAsOf(Carbon $asOf): array
    {
        $t = $asOf->toDateTimeString();
        $named = [
            't' => $t,
            't30' => $asOf->copy()->subDays(30)->toDateTimeString(),
            't3m' => $asOf->copy()->subMonthsNoOverflow(3)->toDateTimeString(),
        ];
        $rdSql = KamusMetrikQuery::rdSql('w');

        // 1. Posisi SPK: dikelompokkan per kombinasi flag, lalu dijumlah di PHP.
        $logWhere = self::STATUS_LOG_WHERE;
        $logStep = self::STATUS_LOG_STEP;

        $rows = self::select("
            SELECT x.st, x.prev, x.is_rd, x.not_taken, x.in_rack, x.in_rack_sepatu, x.recent30, x.umur_le30,
                x.selesai_menit, x.selesai_invoice, x.selesai_basis, COUNT(*) AS n
            FROM (
                SELECT s.*,
                    -- Ditahan CX: tahap mengikuti status terakhir sebelum ditahan (setara work_orders.previous_status)
                    CASE WHEN s.st IN ('CX_FOLLOWUP', 'HOLD_FOR_CX') THEN
                        (SELECT {$logStep} FROM work_order_logs l
                          WHERE l.work_order_id = s.id AND {$logWhere} AND l.created_at <= :t
                            AND l.step NOT IN ('CX_FOLLOWUP', 'HOLD_FOR_CX') AND l.action <> 'QC_REJECTED'
                          ORDER BY l.created_at DESC, l.id DESC LIMIT 1)
                    END AS prev,
                    -- Rak selesai (SELESAI, belum keluar pada T): umur dan invoice untuk aging (No. 50) dan piutang (No. 90).
                    -- Umur dari finished_date; bila kosong / setelah T: waktu log SELESAI, lalu created_at (SPK GR).
                    CASE WHEN s.st = 'SELESAI' AND s.not_taken THEN TIMESTAMPDIFF(MINUTE,
                        CASE WHEN s.finished_date IS NOT NULL AND s.finished_date <= :t THEN s.finished_date
                             WHEN s.log_step = 'SELESAI' THEN s.log_at
                             ELSE s.wo_created END, :t) END AS selesai_menit,
                    CASE WHEN s.st = 'SELESAI' AND s.not_taken THEN s.invoice_id END AS selesai_invoice,
                    CASE WHEN s.st = 'SELESAI' AND s.not_taken THEN
                        CASE WHEN s.finished_date IS NOT NULL AND s.finished_date <= :t THEN 'finished_date'
                             WHEN s.log_step = 'SELESAI' THEN 'log_selesai'
                             ELSE 'created_at' END END AS selesai_basis
                FROM (
                    SELECT y.id, ".self::STATUS_AT_T_SQL." AS st,
                        y.is_rd,
                        (y.taken_date IS NULL OR y.taken_date > :t) AS not_taken,
                        y.in_rack,
                        y.in_rack_sepatu,
                        (y.finished_date IS NOT NULL AND y.finished_date >= :t30) AS recent30,
                        (y.created_at > :t30) AS umur_le30,
                        y.finished_date, y.log_step, y.log_at, y.created_at AS wo_created, y.invoice_id
                    FROM (
                        SELECT b.*,
                            SUBSTRING(b.log_x, 32) AS log_step,
                            CAST(LEFT(b.log_x, 19) AS DATETIME) AS log_at
                        FROM (
                            SELECT w.id, w.status, w.spk_number, w.finished_date, w.taken_date, w.updated_at, w.donated_at, w.created_at,
                                w.invoice_id,
                                {$rdSql} AS is_rd,
                                -- Log status terakhir <= T dalam satu subquery: 'Y-m-d H:i:s' + id (12 digit) + step
                                (SELECT MAX(CONCAT(DATE_FORMAT(l.created_at, '%Y-%m-%d %H:%i:%s'), LPAD(l.id, 12, '0'), {$logStep}))
                                  FROM work_order_logs l
                                  WHERE l.work_order_id = w.id AND {$logWhere} AND l.created_at <= :t) AS log_x,
                                EXISTS (SELECT 1 FROM work_order_logs l
                                  WHERE l.work_order_id = w.id AND {$logWhere}) AS has_any_log,
                                EXISTS (SELECT 1 FROM storage_assignments sa
                                  WHERE sa.work_order_id = w.id AND sa.created_at <= :t
                                    AND (sa.retrieved_at IS NULL OR sa.retrieved_at > :t)) AS in_rack,
                                -- Khusus rak sepatu (kategori shoes); in_rack di atas mencakup semua kategori.
                                EXISTS (SELECT 1 FROM storage_assignments sa
                                  WHERE sa.work_order_id = w.id AND sa.category = 'shoes' AND sa.created_at <= :t
                                    AND (sa.retrieved_at IS NULL OR sa.retrieved_at > :t)) AS in_rack_sepatu
                            FROM work_orders w
                            WHERE w.created_at <= :t AND (w.deleted_at IS NULL OR w.deleted_at > :t)
                              -- Tidak mungkin terhitung pada T: sudah berstatus akhir sebelum T.
                              -- (SPK yang sudah keluar tetap dievaluasi: wipSnapshot() menghitung SPK diambil
                              -- yang statusnya bukan SELESAI, mis. REVISI.)
                              AND NOT (w.status IN ('BATAL', 'DONASI', 'HISTORY') AND COALESCE(w.donated_at, w.updated_at) <= :t)
                        ) b
                    ) y
                ) s
            ) x
            GROUP BY x.st, x.prev, x.is_rd, x.not_taken, x.in_rack, x.in_rack_sepatu, x.recent30, x.umur_le30,
                x.selesai_menit, x.selesai_invoice, x.selesai_basis
        ", $named);

        $pendingLe30 = $pendingGt30 = $siapKirim = $finishedNotStored = $readyForPickup = $readyForPickupSepatu = 0;
        $wip = array_fill_keys(KamusMetrikQuery::WIP_TAHAP_KEYS, 0);
        $agingHari = [];      // umur (hari) tiap SPK di rak selesai
        $rakInvoice = [];     // invoice_id => jumlah SPK di rak selesai

        foreach ($rows as $r) {
            $n = (int) $r->n;
            $st = (string) $r->st;
            $notTaken = (bool) $r->not_taken;

            if ($st === 'SPK_PENDING') {
                $r->umur_le30 ? $pendingLe30 += $n : $pendingGt30 += $n;
            }
            if ($st === 'SELESAI' && $notTaken) {
                $r->in_rack ? $readyForPickup += $n : $finishedNotStored += $n;
                if ($r->in_rack_sepatu) {
                    $readyForPickupSepatu += $n;
                }
                if ($r->recent30) {
                    $siapKirim += $n;
                }
                for ($i = 0; $i < $n; $i++) {
                    $agingHari[] = max(0, (int) $r->selesai_menit) / 1440;
                }
                if ($r->selesai_invoice !== null) {
                    $rakInvoice[(int) $r->selesai_invoice] = ($rakInvoice[(int) $r->selesai_invoice] ?? 0) + $n;
                }
            }
            // Pemetaan sama dengan wipSnapshot(); prev = status sebelum ditahan CX (null → reject QC awal → fisik_tiba).
            $tahap = KamusMetrikQuery::wipTahapFor($st, $r->prev, (bool) $r->is_rd, ! $notTaken);
            if ($tahap !== null) {
                $wip[$tahap] += $n;
            }
        }

        // 2. Isi rak dari riwayat assignment (created_at = waktu simpan asli).
        $sa = self::select("
            SELECT
                COALESCE(SUM(category = 'shoes'), 0) AS shoes,
                COALESCE(SUM(category = 'shoes' AND created_at <= :t3m), 0) AS donasi,
                COALESCE(SUM(category = 'before'), 0) AS before_,
                COALESCE(SUM(category = 'accessories'), 0) AS accessories
            FROM storage_assignments
            WHERE created_at <= :t AND (retrieved_at IS NULL OR retrieved_at > :t)
        ", $named)[0];

        $manual = (int) self::select('
            SELECT COUNT(*) AS n FROM manual_storage_items
            WHERE in_date <= :t AND (out_date IS NULL OR out_date > :t) AND (deleted_at IS NULL OR deleted_at > :t)
        ', $named)[0]->n;

        // 3. Pendekatan: kapasitas rak (perubahan capacity/status rak tidak tercatat) dan shipping (tanpa verified_at).
        $capacity = (int) self::select("
            SELECT COALESCE(SUM(capacity), 0) AS n FROM storage_racks
            WHERE category <> 'accessories' AND status = 'active'
              AND created_at <= :t AND (deleted_at IS NULL OR deleted_at > :t)
        ", $named)[0]->n;

        $shipping = (int) self::select('
            SELECT COUNT(*) AS n FROM shippings
            WHERE created_at <= :t AND (is_verified = 0 OR updated_at > :t)
        ', $named)[0]->n;

        $shoes = (int) $sa->shoes;
        $before = (int) $sa->before_;

        return [
            'as_of' => $t,
            'pending_reception_total' => $pendingLe30 + $pendingGt30,
            'pending_reception_le_30_hari' => $pendingLe30,
            'pending_reception_gt_30_hari' => $pendingGt30,
            'siap_kirim_aktif' => $siapKirim,
            'finished_not_stored' => $finishedNotStored,
            'ready_for_pickup' => $readyForPickup,
            // SELESAI, belum diambil, dan sepatunya ada di rak sepatu (≤ shoes_in_rack_count).
            'ready_for_pickup_sepatu' => $readyForPickupSepatu,
            'shoes_in_rack_count' => $shoes,
            'donation_candidates_count' => (int) $sa->donasi,
            'inbound_inventory' => $before,
            'manual_items_aktif' => $manual,
            'accessories_aktif' => (int) $sa->accessories,
            'finish_inventory' => $shoes + $manual,
            'stored_items' => $before + $shoes + $manual,
            'total_capacity' => $capacity,
            'shipping_pending' => $shipping,
            'wip_tahap' => $wip,
            'metode' => self::METODE_POSISI,
            // Kamus v3 No. 50 & No. 90 (Tahap 3)
            'aging_rak_selesai' => self::agingSummary($agingHari, [3, 14, 30]),
            'piutang_rak_selesai' => self::piutangRakSelesai($rakInvoice, $t),
        ];
    }

    /** Ringkasan umur (hari): n, rata-rata, median, dan jumlah yang lebih dari tiap ambang. */
    private static function agingSummary(array $hari, array $ambang): array
    {
        $out = [
            'n' => count($hari),
            'avg_hari' => self::avg($hari),
            'median_hari' => self::percentile($hari, 0.5),
        ];
        foreach ($ambang as $a) {
            $out["lebih_{$a}_hari"] = count(array_filter($hari, fn ($h) => $h > $a));
        }

        return $out;
    }

    /**
     * Kamus v3 No. 90 di level invoice: invoice (bukan Batal) yang punya SPK di rak selesai pada T dan belum lunas pada T.
     * Tagihan = total_amount + shipping_cost − discount (nilai sekarang); dibayar = pembayaran terverifikasi dengan paid_at <= T.
     * Sisa tiap invoice dihitung sekali walau SPK-nya di rak lebih dari satu.
     *
     * @param  array<int,int>  $rakInvoice  invoice_id => jumlah SPK di rak selesai
     */
    private static function piutangRakSelesai(array $rakInvoice, string $t): array
    {
        $out = ['invoice' => 0, 'pasang_di_rak' => 0, 'rupiah_tagihan' => 0.0, 'rupiah_dibayar' => 0.0,
            'rupiah_sisa' => 0.0, 'invoice_sebagian_spk_sudah_keluar' => 0];
        if (! $rakInvoice) {
            return $out;
        }

        $ids = implode(',', array_map('intval', array_keys($rakInvoice)));
        $rows = self::select('
            SELECT i.id,
                   i.total_amount + COALESCE(i.shipping_cost, 0) - COALESCE(i.discount, 0) AS tagihan,
                   '.self::paidAtSql('i.id', ':t')." AS dibayar,
                   EXISTS (SELECT 1 FROM work_orders wk
                            WHERE wk.invoice_id = i.id AND wk.deleted_at IS NULL AND wk.taken_date <= :t) AS ada_keluar
            FROM invoices i
            WHERE i.id IN ({$ids}) AND COALESCE(i.status, '') NOT IN ('Batal', 'BATAL')
        ", ['t' => $t]);

        foreach ($rows as $r) {
            $tagihan = (float) $r->tagihan;
            $dibayar = (float) $r->dibayar;
            if ($dibayar >= $tagihan) {
                continue; // sudah lunas pada T
            }
            $out['invoice']++;
            $out['pasang_di_rak'] += $rakInvoice[(int) $r->id];
            $out['rupiah_tagihan'] += $tagihan;
            $out['rupiah_dibayar'] += $dibayar;
            $out['rupiah_sisa'] += $tagihan - $dibayar;
            $out['invoice_sebagian_spk_sudah_keluar'] += (int) $r->ada_keluar;
        }

        return $out;
    }

    /**
     * Pembayaran terverifikasi milik invoice (level invoice + level SPK yang invoice_id-nya NULL)
     * dengan paid_at <= kolom waktu $atColumn. Sama dengan cara Invoice::syncFinancials() menjumlah paid_amount.
     */
    private static function paidAtSql(string $invoiceIdColumn, string $atColumn): string
    {
        return "(SELECT COALESCE(SUM(op.amount_total), 0) FROM order_payments op
                  WHERE op.is_verified = 1 AND op.paid_at <= {$atColumn}
                    AND (op.invoice_id = {$invoiceIdColumn}
                         OR (op.invoice_id IS NULL AND op.work_order_id IN
                             (SELECT w3.id FROM work_orders w3 WHERE w3.invoice_id = {$invoiceIdColumn}))))";
    }

    /**
     * Sepatu keluar (work_orders.taken_date) di [$start, $end]; lunas dihitung di level invoice pada saat keluar.
     * Rupiah memakai atribusi "keluar_pertama": invoice dihitung di periode tempat SPK-nya pertama kali keluar,
     * dan pembayarannya diukur pada waktu keluar pertama itu.
     */
    public static function sepatuKeluarDetail(Carbon $start, Carbon $end): array
    {
        $named = ['s' => $start->toDateTimeString(), 'e' => $end->toDateTimeString()];
        $tagihan = 'i.total_amount + COALESCE(i.shipping_cost, 0) - COALESCE(i.discount, 0)';

        $pasang = self::select('
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(k.inv_id IS NULL), 0) AS tanpa_invoice,
                COALESCE(SUM(k.is_gr), 0) AS garansi,
                COALESCE(SUM(k.inv_id IS NOT NULL AND k.paid_exit >= k.tagihan), 0) AS lunas,
                COALESCE(SUM(k.inv_id IS NOT NULL AND k.paid_exit < k.tagihan), 0) AS belum_lunas
            FROM (
                SELECT i.id AS inv_id, '.self::gr('w')." AS is_gr,
                       {$tagihan} AS tagihan,
                       ".self::paidAtSql('i.id', 'w.taken_date').' AS paid_exit
                FROM work_orders w
                LEFT JOIN invoices i ON i.id = w.invoice_id
                WHERE w.deleted_at IS NULL AND w.taken_date BETWEEN :s AND :e
            ) k
        ', $named)[0];

        $rupiah = self::select("
            SELECT
                COUNT(*) AS invoice_count,
                COALESCE(SUM(v.tagihan), 0) AS tagihan,
                COALESCE(SUM(LEAST(v.paid_exit, v.tagihan)), 0) AS dibayar,
                COALESCE(SUM(GREATEST(v.tagihan - v.paid_exit, 0)), 0) AS sisa,
                COALESCE(SUM(v.tagihan > v.paid_exit), 0) AS belum_lunas
            FROM (
                SELECT u.inv_id, {$tagihan} AS tagihan,
                       ".self::paidAtSql('u.inv_id', 'u.first_taken').' AS paid_exit
                FROM (
                    SELECT w.invoice_id AS inv_id, MIN(w.taken_date) AS first_taken
                    FROM work_orders w
                    WHERE w.deleted_at IS NULL AND w.invoice_id IS NOT NULL AND w.taken_date IS NOT NULL
                    GROUP BY w.invoice_id
                    HAVING MIN(w.taken_date) BETWEEN :s AND :e
                ) u
                JOIN invoices i ON i.id = u.inv_id
            ) v
        ', $named)[0];

        return [
            'pasang_total' => (int) $pasang->total,
            'pasang_lunas_saat_keluar' => (int) $pasang->lunas,
            'pasang_belum_lunas_saat_keluar' => (int) $pasang->belum_lunas,
            'pasang_tanpa_invoice' => (int) $pasang->tanpa_invoice,
            'pasang_garansi' => (int) $pasang->garansi,
            'invoice_count' => (int) $rupiah->invoice_count,
            'rupiah_tagihan' => (float) $rupiah->tagihan,
            'rupiah_dibayar_saat_keluar' => (float) $rupiah->dibayar,
            'rupiah_sisa_saat_keluar' => (float) $rupiah->sisa,
            'invoice_belum_lunas_saat_keluar' => (int) $rupiah->belum_lunas,
            'aturan_atribusi_invoice' => 'keluar_pertama',
            // Kamus v3 No. 50 (Tahap 3): umur = taken_date − finished_date, SPK tanpa finished_date tidak dihitung.
            'aging_selesai_ke_keluar' => self::agingSelesaiKeKeluar($named),
        ];
    }

    private static function agingSelesaiKeKeluar(array $named): array
    {
        $rows = self::select('
            SELECT TIMESTAMPDIFF(MINUTE, w.finished_date, w.taken_date) AS menit
            FROM work_orders w
            WHERE w.deleted_at IS NULL AND w.taken_date BETWEEN :s AND :e AND w.finished_date IS NOT NULL
        ', $named);

        // Selisih negatif (taken_date sebelum finished_date, data tidak urut) dihitung 0.
        return self::agingSummary(array_map(fn ($r) => max(0, (int) $r->menit) / 1440, $rows), [3]);
    }

    /**
     * Kamus v3 No. 43 (Inbound Transit Time-Lag): SPK non-GR yang TIBA di gudang pada [$start, $end].
     * Tiba = log DITERIMA/STATUS_CHANGE pertama, fallback warehouse_qc_at (sama dengan kedatangan).
     * Tanpa SPK terhapus (pada akhir periode) dan BATAL (updated_at <= akhir periode).
     */
    public static function transitPeriode(Carbon $start, Carbon $end): array
    {
        $named = ['s' => $start->toDateTimeString(), 'e' => $end->toDateTimeString()];

        // Kandidat dipersempit dengan entry_date / warehouse_qc_at: entry_date ditimpa saat DITERIMA,
        // jadi SPK yang tiba di periode pasti punya salah satunya >= awal periode.
        $rows = self::select("
            SELECT z.created_at, z.tiba_at, DATEDIFF(COALESCE(z.warehouse_qc_at, z.entry_date), z.created_at) AS hari_kamus
            FROM (
                SELECT w.created_at, w.entry_date, w.warehouse_qc_at,
                    COALESCE(
                        (SELECT MIN(l.created_at) FROM work_order_logs l
                          WHERE l.work_order_id = w.id AND l.step = 'DITERIMA' AND l.action = 'STATUS_CHANGE'),
                        w.warehouse_qc_at
                    ) AS tiba_at
                FROM work_orders w
                WHERE w.created_at <= :e
                  AND (w.entry_date >= :s OR w.warehouse_qc_at >= :s)
                  AND (w.deleted_at IS NULL OR w.deleted_at > :e)
                  AND NOT (w.status = 'BATAL' AND w.updated_at <= :e)
                  AND NOT ".self::gr('w').'
            ) z
            WHERE z.tiba_at BETWEEN :s AND :e
        ', $named);

        $hari = $hariKerja = $hariKamus = [];
        foreach ($rows as $r) {
            $created = Carbon::parse($r->created_at);
            $tiba = Carbon::parse($r->tiba_at);
            $hari[] = max(0, $created->diffInMinutes($tiba, false)) / 1440;
            $hariKerja[] = self::hariKerja($created, $tiba);
            $hariKamus[] = (int) $r->hari_kamus;
        }

        return [
            'spk_tiba' => count($rows),
            'avg_hari' => self::avg($hari),
            'median_hari' => self::percentile($hari, 0.5),
            'p80_hari' => self::percentile($hari, 0.8),
            'p90_hari' => self::percentile($hari, 0.9),
            'avg_hari_kerja' => self::avg($hariKerja),
            'avg_hari_rumus_kamus' => self::avg($hariKamus),
            'penanda_tiba' => 'log_diterima_status_change_fallback_warehouse_qc_at',
            'hari_kerja' => 'tanpa_minggu_tanpa_libur_nasional',
        ];
    }

    /**
     * Selisih hari (desimal) tanpa hari Minggu, sama dengan aturan Invoice::addWorkingDays().
     * ERP tidak punya tabel hari libur, jadi libur nasional tidak dikecualikan.
     */
    public static function hariKerja(Carbon $from, Carbon $to): float
    {
        if ($to->lte($from)) {
            return 0.0;
        }
        $detik = 0;
        $cursor = $from->copy();
        while ($cursor->lt($to)) {
            $akhirHari = $cursor->copy()->addDay()->startOfDay();
            $batas = $akhirHari->lt($to) ? $akhirHari : $to;
            if (! $cursor->isSunday()) {
                $detik += $cursor->diffInSeconds($batas);
            }
            $cursor = $batas;
        }

        return $detik / 86400;
    }

    /**
     * SPK non-GR yang terbit di [$start, $end] (dan sudah terbit pada $asOf), dievaluasi pada $asOf.
     * Tiba = log DITERIMA/STATUS_CHANGE pertama; fallback warehouse_qc_at bila log tidak ada.
     */
    public static function kedatangan(Carbon $start, Carbon $end, Carbon $asOf): array
    {
        $named = [
            's' => $start->toDateTimeString(),
            'e' => $end->toDateTimeString(),
            't' => $asOf->toDateTimeString(),
            't7' => $asOf->copy()->subDays(7)->toDateTimeString(),
        ];

        $rows = self::select("
            SELECT
                CASE
                    WHEN z.tiba_at IS NOT NULL AND z.tiba_at <= :t THEN 'tiba'
                    WHEN z.status = 'BATAL' AND z.updated_at <= :t THEN 'batal'
                    WHEN z.created_at >= :t7 THEN 'le7'
                    ELSE 'gt7'
                END AS kelompok,
                CASE WHEN z.tiba_at IS NOT NULL AND z.tiba_at <= :t
                     THEN TIMESTAMPDIFF(MINUTE, z.created_at, z.tiba_at) END AS menit_tiba
            FROM (
                SELECT w.created_at, w.status, w.updated_at,
                    COALESCE(
                        (SELECT MIN(l.created_at) FROM work_order_logs l
                          WHERE l.work_order_id = w.id AND l.step = 'DITERIMA' AND l.action = 'STATUS_CHANGE'),
                        w.warehouse_qc_at
                    ) AS tiba_at
                FROM work_orders w
                WHERE w.created_at BETWEEN :s AND :e AND w.created_at <= :t
                  AND (w.deleted_at IS NULL OR w.deleted_at > :t)
                  AND NOT ".self::gr('w').'
            ) z
        ', $named);

        $count = ['tiba' => 0, 'le7' => 0, 'gt7' => 0, 'batal' => 0];
        $hari = [];
        foreach ($rows as $r) {
            $count[$r->kelompok]++;
            if ($r->menit_tiba !== null) {
                $hari[] = max(0, (int) $r->menit_tiba) / 1440;
            }
        }

        return [
            'spk_terbit_non_gr' => count($rows),
            'sudah_tiba' => $count['tiba'],
            'belum_tiba_le_7_hari' => $count['le7'],
            'belum_tiba_gt_7_hari' => $count['gt7'],
            'batal' => $count['batal'],
            'hari_tiba' => [
                'median' => self::percentile($hari, 0.5),
                'p80' => self::percentile($hari, 0.8),
                'p90' => self::percentile($hari, 0.9),
                'n' => count($hari),
            ],
            'penanda_tiba' => 'log_diterima_status_change_fallback_warehouse_qc_at',
        ];
    }

    /**
     * Lama simpan di rak untuk assignment yang diambil (retrieved_at) di [$start, $end].
     * dwell = retrieved_at - created_at (bukan stored_at, lihat catatan kelas).
     */
    public static function efisiensiV2(Carbon $start, Carbon $end): array
    {
        $rows = self::select('
            SELECT category, TIMESTAMPDIFF(MINUTE, created_at, retrieved_at) AS menit
            FROM storage_assignments
            WHERE retrieved_at BETWEEN :s AND :e AND retrieved_at >= created_at
        ', ['s' => $start->toDateTimeString(), 'e' => $end->toDateTimeString()]);

        $semua = [];
        $sepatu = [];
        foreach ($rows as $r) {
            $jam = (int) $r->menit / 60;
            $semua[] = $jam;
            if ($r->category === 'shoes') {
                $sepatu[] = $jam;
            }
        }

        return [
            'avg_dwell_hours_sepatu' => self::avg($sepatu),
            'median_dwell_hours_sepatu' => self::percentile($sepatu, 0.5),
            'n_sepatu' => count($sepatu),
            'avg_dwell_hours_semua' => self::avg($semua),
            'n_semua' => count($semua),
        ];
    }

    /**
     * Arus SPK gudang <-> workshop di [$start, $end] dari work_order_logs.
     * workshop_manifests dipakai dua arah dan work_orders.workshop_manifest_id ditimpa oleh manifest outbound,
     * sehingga total_spk_sent di /warehouse-manifest-summary mencampur dua arah.
     */
    public static function pengirimanWorkshop(Carbon $start, Carbon $end): array
    {
        $named = ['s' => $start->toDateTimeString(), 'e' => $end->toDateTimeString()];

        $log = self::select("
            SELECT
                COUNT(DISTINCT CASE WHEN step = 'OTW_WORKSHOP' AND action = 'STATUS_CHANGE' THEN work_order_id END) AS otw,
                COUNT(DISTINCT CASE WHEN action = 'MANIFEST_GENERATED' THEN work_order_id END) AS out_dibuat,
                COUNT(DISTINCT CASE WHEN action = 'OUTBOUND_MANIFEST_RECEIVED' THEN work_order_id END) AS out_diterima
            FROM work_order_logs
            WHERE created_at BETWEEN :s AND :e
              AND ((step = 'OTW_WORKSHOP' AND action = 'STATUS_CHANGE')
                   OR action IN ('MANIFEST_GENERATED', 'OUTBOUND_MANIFEST_RECEIVED'))
        ", $named)[0];

        $pertama = (int) self::select("
            SELECT COUNT(*) AS n FROM (
                SELECT work_order_id FROM work_order_logs
                WHERE step = 'OTW_WORKSHOP' AND action = 'STATUS_CHANGE'
                GROUP BY work_order_id
                HAVING MIN(created_at) BETWEEN :s AND :e
            ) f
        ", $named)[0]->n;

        $manifest = self::select("
            SELECT
                COALESCE(SUM(manifest_number NOT LIKE 'MNF-OUT-%'), 0) AS masuk,
                COALESCE(SUM(manifest_number LIKE 'MNF-OUT-%'), 0) AS keluar
            FROM workshop_manifests
            WHERE deleted_at IS NULL AND dispatched_at BETWEEN :s AND :e
        ", $named)[0];

        return [
            'spk_dikirim_ke_workshop' => $pertama,
            'spk_dikirim_ke_workshop_termasuk_ulang' => (int) $log->otw,
            'spk_manifest_outbound_dibuat' => (int) $log->out_dibuat,
            'spk_masuk_gudang_finish' => (int) $log->out_diterima,
            'manifest_gudang_ke_workshop' => (int) $manifest->masuk,
            'manifest_qc_ke_gudang' => (int) $manifest->keluar,
        ];
    }

    private static function avg(array $values): float
    {
        return $values ? round(array_sum($values) / count($values), 1) : 0.0;
    }

    /** Persentil dengan interpolasi linear (sama dengan numpy default). */
    public static function percentile(array $values, float $p): float
    {
        if (! $values) {
            return 0.0;
        }
        sort($values);
        $k = (count($values) - 1) * $p;
        $lo = (int) floor($k);
        $hi = min($lo + 1, count($values) - 1);

        return round($values[$lo] + ($values[$hi] - $values[$lo]) * ($k - $lo), 2);
    }
}
