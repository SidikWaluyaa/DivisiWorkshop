<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Report Harian per divisi untuk InsightSW (GET /api/v1/daily-report). Hanya SELECT agregat.
 * Semua rentang waktu dalam WIB; untuk hari ini, data otomatis "sampai saat request".
 */
class DailyReportQuery
{
    /** Nilai varchar "Rp. 60.000" → angka (buang semua non-digit; MySQL 5.5 belum punya REGEXP_REPLACE). */
    private const OTO_PRICE_SQL = "CAST(REPLACE(REPLACE(REPLACE(REPLACE(o.total_oto_price, 'Rp', ''), '.', ''), ' ', ''), ',', '') AS UNSIGNED)";

    /** OTO dianggap closing bila customer sudah menyetujui (status ACCEPTED atau tahap sesudahnya). */
    private const OTO_CLOSING_STATUS = "('ACCEPTED', 'IN_PROGRESS', 'COMPLETED')";

    /** Step log saat jasa masih tahap penyusunan awal (CS/penerimaan/assessment), bukan tambah jasa. */
    private const STEP_AWAL = "('SPK_PENDING', 'DITERIMA', 'ASSESSMENT')";

    /** Blok CS: closing = baris cs_spk menurut created_at (semua status, kecuali yang dihapus). */
    public static function cs(Carbon $date): array
    {
        [$s, $e] = self::range($date);

        $perJam = array_fill_keys(array_map('strval', range(0, 23)), 0);
        foreach (DB::select('SELECT HOUR(created_at) AS jam, COUNT(*) AS n FROM cs_spk
                WHERE deleted_at IS NULL AND created_at BETWEEN ? AND ? GROUP BY HOUR(created_at)', [$s, $e]) as $r) {
            $perJam[(string) $r->jam] = (int) $r->n;
        }

        $perChannel = ['ONLINE' => 0, 'OFFLINE' => 0, 'FOLLOW_UP' => 0, 'LAINNYA' => 0];
        foreach (DB::select("SELECT CASE WHEN UPPER(TRIM(l.channel)) IN ('ONLINE', 'OFFLINE', 'FOLLOW_UP') THEN UPPER(TRIM(l.channel)) ELSE 'LAINNYA' END AS ch,
                    COUNT(*) AS n
                FROM cs_spk s LEFT JOIN cs_leads l ON l.id = s.cs_lead_id
                WHERE s.deleted_at IS NULL AND s.created_at BETWEEN ? AND ? GROUP BY ch", [$s, $e]) as $r) {
            $perChannel[$r->ch] = (int) $r->n;
        }

        $senin = $date->copy()->startOfWeek(Carbon::MONDAY);
        $harian = [];
        foreach (DB::select('SELECT DATE(created_at) AS tgl, COUNT(*) AS n FROM cs_spk
                WHERE deleted_at IS NULL AND created_at BETWEEN ? AND ? GROUP BY DATE(created_at)',
            [$senin->toDateTimeString(), $e]) as $r) {
            $harian[$r->tgl] = (int) $r->n;
        }
        $mingguIni = [];
        for ($d = $senin->copy(); $d->lte($date); $d->addDay()) {
            $mingguIni[] = ['tanggal' => $d->toDateString(), 'closing' => $harian[$d->toDateString()] ?? 0];
        }

        // (object): key "0"–"23" berurutan akan di-encode sebagai list JSON; kontrak meminta objek ber-key jam.
        return ['closing_per_jam' => (object) $perJam, 'per_channel' => $perChannel, 'minggu_ini' => $mingguIni];
    }

    /** Blok OTO: angka hari itu + akumulasi tanggal 1 s.d. hari itu. */
    public static function oto(Carbon $date): array
    {
        [$s, $e] = self::range($date);

        return self::otoRange($s, $e) + [
            'bulan_ini' => self::otoRange($date->copy()->startOfMonth()->toDateTimeString(), $e),
        ];
    }

    private static function otoRange(string $s, string $e): array
    {
        // Penawaran: semua OTO yang dibuat, termasuk yang kemudian dibatalkan/dihapus (tetap pernah ditawarkan).
        $penawaran = (int) DB::selectOne('SELECT COUNT(*) AS n FROM otos WHERE created_at BETWEEN ? AND ?', [$s, $e])->n;

        $closing = DB::selectOne('SELECT COUNT(DISTINCT o.work_order_id) AS spk, COALESCE(SUM('.self::OTO_PRICE_SQL.'), 0) AS omset
            FROM otos o
            WHERE o.deleted_at IS NULL AND o.status IN '.self::OTO_CLOSING_STATUS.'
              AND o.customer_responded_at BETWEEN ? AND ?', [$s, $e]);

        // Pembayaran OTO tidak menyimpan work_order_id (hanya invoice), jadi satu baris = satu pembayaran OTO.
        $bayar = DB::selectOne("SELECT COUNT(*) AS n, COALESCE(SUM(amount_total), 0) AS nominal FROM order_payments
            WHERE type = 'OTO' AND is_verified = 1 AND paid_at BETWEEN ? AND ?", [$s, $e]);

        return [
            'penawaran' => $penawaran,
            'closing_spk' => (int) $closing->spk,
            'closing_omset' => (int) $closing->omset,
            'bayar_spk' => (int) $bayar->n,
            'bayar_nominal' => (int) $bayar->nominal,
            'sepatu_keluar_ws' => self::sepatuKeluarWs($s, $e),
        ];
    }

    /**
     * Sepatu keluar workshop = SPK yang diterbitkan dalam manifest outbound (log MANIFEST_GENERATED, step OUTBOUND_QC).
     * work_orders.workshop_manifest_id ditimpa manifest berikutnya, jadi sumbernya log; nomor manifest diambil dari
     * deskripsi log ("... Manifest Outbound #MNF-OUT-xxx oleh ...") untuk membuang manifest yang dihapus.
     * Satu SPK dihitung sekali per hari (SPK yang keluar lagi di hari lain, mis. setelah revisi, dihitung lagi).
     */
    private static function sepatuKeluarWs(string $s, string $e): int
    {
        return (int) DB::selectOne("SELECT COUNT(DISTINCT l.work_order_id, DATE(l.created_at)) AS n
            FROM work_order_logs l
            LEFT JOIN workshop_manifests m
                ON m.manifest_number = SUBSTRING_INDEX(SUBSTRING_INDEX(l.description, '#', -1), ' ', 1)
            WHERE l.action = 'MANIFEST_GENERATED' AND l.created_at BETWEEN ? AND ?
              AND (m.id IS NULL OR m.deleted_at IS NULL)", [$s, $e])->n;
    }

    /**
     * Tambah jasa = baris work_order_services yang ditambahkan setelah SPK berjalan, pada tanggal ditambahkan di ERP:
     * stasiun Tambah Jasa (is_tambah_jasa), tambah jasa CX (is_cx_additional), atau tambah jasa lewat admin/sortir
     * (log SERVICE_ADDED/UPSELL setelah tahap assessment). Jasa awal SPK dan jasa dari OTO yang disetujui tidak dihitung.
     */
    public static function tambahJasa(Carbon $date): array
    {
        [$s, $e] = self::range($date);

        $r = DB::selectOne("SELECT COUNT(DISTINCT s.work_order_id) AS spk, COUNT(*) AS jasa, COALESCE(SUM(s.cost), 0) AS nominal
            FROM work_order_services s
            WHERE s.created_at BETWEEN ? AND ?
              AND (
                    s.service_details LIKE '%\"is_tambah_jasa\":true%'
                 OR s.service_details LIKE '%\"is_cx_additional\":true%'
                 OR EXISTS (
                        SELECT 1 FROM work_order_logs l
                        WHERE l.work_order_id = s.work_order_id
                          AND l.action IN ('SERVICE_ADDED', 'UPSELL')
                          AND l.step NOT IN ".self::STEP_AWAL.'
                          AND l.created_at BETWEEN s.created_at - INTERVAL 5 SECOND AND s.created_at + INTERVAL 5 SECOND
                    )
              )', [$s, $e]);

        return ['spk' => (int) $r->spk, 'jasa' => (int) $r->jasa, 'nominal' => (int) round((float) $r->nominal)];
    }

    /** QC After: konfirmasi after tahap OTO yang dibuat hari itu; sudah_qc = SPK-nya sudah lulus QC final. */
    public static function qcAfter(Carbon $date): array
    {
        [$s, $e] = self::range($date);

        $r = DB::selectOne("SELECT COUNT(*) AS after_n, COALESCE(SUM(w.qc_final_completed_at IS NOT NULL), 0) AS qc
            FROM cx_after_confirmations c
            LEFT JOIN work_orders w ON w.id = c.work_order_id
            WHERE c.stage_type = 'OTO' AND c.created_at BETWEEN ? AND ?", [$s, $e]);

        return ['after' => (int) $r->after_n, 'sudah_qc' => (int) $r->qc];
    }

    /** Workshop Hijau: jumlah SPK yang menyelesaikan tiap pekerjaan pendukung pada tanggal itu. */
    public static function pendukung(Carbon $date): array
    {
        [$s, $e] = self::range($date);
        $kolom = [
            'cuci' => 'prep_washing_completed_at',
            'clean_up' => 'qc_cleanup_completed_at',
            'jahit_sol' => 'qc_jahit_completed_at',
            'bongkar_sol' => 'prep_sol_completed_at',
            'bongkar_upper' => 'prep_upper_completed_at',
        ];

        // Satu COUNT per kolom (UNION ALL) agar tiap kolom memakai indeks/rentang sendiri tanpa OR besar.
        $sql = collect($kolom)->map(fn ($c, $k) => "SELECT '$k' AS k, COUNT(*) AS n FROM work_orders
            WHERE deleted_at IS NULL AND $c BETWEEN ? AND ?")->implode(' UNION ALL ');
        $bindings = array_merge(...array_fill(0, count($kolom), [$s, $e]));

        $out = array_fill_keys(array_keys($kolom), 0);
        foreach (DB::select($sql, $bindings) as $r) {
            $out[$r->k] = (int) $r->n;
        }

        return $out;
    }

    /** Status gudang yang dipantau Kontrol Algoritma (posisi saat request). */
    private const STATUS_TERTAHAN = ['DITERIMA', 'ASSESSMENT', 'WAITING_PAYMENT'];

    private const STATUS_BELUM_KIRIM = ['READY_TO_DISPATCH', 'CX_FOLLOWUP'];

    /** Arus gudang per hari: step log STATUS_CHANGE → key response. */
    private const ARUS_GUDANG = [
        'DITERIMA' => 'before',
        'READY_TO_DISPATCH' => 'print_spk',
        'OTW_WORKSHOP' => 'kirim_ws',
        'PREPARATION' => 'diterima_ws',
    ];

    /**
     * Blok Gudang: sepatu masuk per channel, arus harian (before → print SPK → kirim WS → diterima WS)
     * dari tanggal 1 sampai $date, dan Kontrol Algoritma (jumlah SPK per status saat ini).
     */
    public static function gudang(Carbon $date): array
    {
        $perHari = self::arusGudangPerHari($date->copy()->startOfMonth(), $date);
        $hariIni = end($perHari);

        return [
            'sepatu_masuk' => self::sepatuMasuk($date),
            'before' => $hariIni['before'],
            'print_spk' => $hariIni['print_spk'],
            'kirim_ws' => $hariIni['kirim_ws'],
            'diterima_ws' => $hariIni['diterima_ws'],
            'bulan_ini' => ['per_hari' => $perHari],
            'kontrol' => self::kontrolGudang(),
        ];
    }

    /**
     * Sepatu masuk = SPK (bukan BATAL, tidak dihapus) dengan entry_date pada tanggal itu, menurut channel lead CS
     * (cs_spk.work_order_id → cs_spk.cs_lead_id; spk_number tidak dipakai karena prefix-nya beda).
     * sd_18 = hanya entry_date sebelum 18.00 (cut-off report manual).
     */
    private static function sepatuMasuk(Carbon $date): array
    {
        [$s, $e] = self::range($date);
        $kosong = ['offline' => 0, 'online' => 0, 'follow_up' => 0, 'lainnya' => 0, 'total' => 0];
        $semua = $kosong;
        $sd18 = $kosong;

        $rows = DB::select("SELECT CASE UPPER(TRIM(COALESCE(l.channel, '')))
                    WHEN 'OFFLINE' THEN 'offline' WHEN 'ONLINE' THEN 'online' WHEN 'FOLLOW_UP' THEN 'follow_up' ELSE 'lainnya' END AS ch,
                COUNT(*) AS n, COALESCE(SUM(TIME(w.entry_date) < '18:00:00'), 0) AS n18
            FROM work_orders w
            LEFT JOIN cs_spk s ON s.work_order_id = w.id AND s.deleted_at IS NULL
            LEFT JOIN cs_leads l ON l.id = s.cs_lead_id
            WHERE w.deleted_at IS NULL AND w.status <> 'BATAL' AND w.entry_date BETWEEN ? AND ?
            GROUP BY ch", [$s, $e]);
        foreach ($rows as $r) {
            $semua[$r->ch] += (int) $r->n;
            $sd18[$r->ch] += (int) $r->n18;
        }
        $semua['total'] = $semua['offline'] + $semua['online'] + $semua['follow_up'] + $semua['lainnya'];
        $sd18['total'] = $sd18['offline'] + $sd18['online'] + $sd18['follow_up'] + $sd18['lainnya'];

        return $semua + ['sd_18' => $sd18];
    }

    /**
     * Jumlah SPK unik per hari dengan log STATUS_CHANGE ke step terkait (log OTW_WORKSHOP/PREPARATION sering tercatat
     * dobel, sehingga dihitung unik per SPK per hari). Setiap tanggal dari $start s.d. $end ikut, termasuk yang 0.
     */
    private static function arusGudangPerHari(Carbon $start, Carbon $end): array
    {
        $steps = "'".implode("', '", array_keys(self::ARUS_GUDANG))."'";
        $data = [];
        foreach (DB::select("SELECT DATE(created_at) AS tgl, step, COUNT(DISTINCT work_order_id) AS n
                FROM work_order_logs
                WHERE action = 'STATUS_CHANGE' AND step IN ($steps) AND created_at BETWEEN ? AND ?
                GROUP BY DATE(created_at), step", [
            $start->copy()->startOfDay()->toDateTimeString(), $end->copy()->endOfDay()->toDateTimeString(),
        ]) as $r) {
            $data[$r->tgl][self::ARUS_GUDANG[$r->step]] = (int) $r->n;
        }

        $perHari = [];
        for ($d = $start->copy()->startOfDay(); $d->lte($end); $d->addDay()) {
            $baris = ['tanggal' => $d->toDateString()];
            foreach (self::ARUS_GUDANG as $key) {
                $baris[$key] = $data[$d->toDateString()][$key] ?? 0;
            }
            $perHari[] = $baris;
        }

        return $perHari;
    }

    /** Kontrol Algoritma: jumlah SPK (tidak dihapus) per status saat request, apa pun tanggal yang diminta. */
    private static function kontrolGudang(): array
    {
        $status = array_merge(self::STATUS_TERTAHAN, self::STATUS_BELUM_KIRIM, ['WAITING_VERIFICATION']);
        $n = array_fill_keys($status, 0);
        $in = "'".implode("', '", $status)."'";
        foreach (DB::select("SELECT status, COUNT(*) AS n FROM work_orders
                WHERE deleted_at IS NULL AND status IN ($in) GROUP BY status") as $r) {
            $n[$r->status] = (int) $r->n;
        }

        $tertahan = array_intersect_key($n, array_flip(self::STATUS_TERTAHAN));
        $tertahan['total'] = array_sum($tertahan);
        $belumKirim = array_intersect_key($n, array_flip(self::STATUS_BELUM_KIRIM));
        $belumKirim['total'] = $tertahan['total'] + array_sum($belumKirim);

        return [
            'diambil_pada' => now()->toDateTimeString(),
            'tertahan' => $tertahan,
            'belum_kirim' => $belumKirim,
            'waiting_verification' => $n['WAITING_VERIFICATION'],
        ];
    }

    /** @return array{0: string, 1: string} awal dan akhir hari (WIB). */
    private static function range(Carbon $date): array
    {
        return [$date->copy()->startOfDay()->toDateTimeString(), $date->copy()->endOfDay()->toDateTimeString()];
    }
}
