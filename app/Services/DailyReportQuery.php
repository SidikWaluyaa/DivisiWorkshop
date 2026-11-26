<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $out = self::tambahJasaRange($s, $e);

        // Tahap 6: akumulasi tanggal 1 s.d. $date dengan rumus yang sama. Bila gagal, key harian tetap dikirim.
        try {
            $out['bulan_ini'] = self::tambahJasaRange($date->copy()->startOfMonth()->toDateTimeString(), $e);
        } catch (\Throwable $ex) {
            Log::error('daily-report: tambah_jasa.bulan_ini gagal dihitung', [
                'tanggal' => $date->toDateString(),
                'error' => $ex->getMessage(),
            ]);
        }

        return $out;
    }

    private static function tambahJasaRange(string $s, string $e): array
    {
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
        // Intake Fisik Kamus No. 32 (sama dengan Dashboard Gudang), dihitung sekali untuk garansi_masuk & intake_fisik.
        $intakeFisik = KamusMetrikQuery::intakeFisik($date->copy()->startOfDay(), $date->copy()->endOfDay());

        return [
            'sepatu_masuk' => self::sepatuMasuk($date),
            'before' => $hariIni['before'],
            'print_spk' => $hariIni['print_spk'],
            'kirim_ws' => $hariIni['kirim_ws'],
            'diterima_ws' => $hariIni['diterima_ws'],
            'bulan_ini' => ['per_hari' => $perHari],
            'kontrol' => self::kontrolGudang(),
            // Tahap 3 (revisi owner): key tambahan, key di atas tetap sama.
            'sepatu_masuk_jenis' => self::sepatuMasukJenis($date),
            'otw_ws' => $hariIni['otw_ws'],
            'sepatu_keluar' => self::sepatuKeluar($date),
            'klaim_garansi' => self::klaimGaransi($date),
            'cancel' => self::cancel($date),
            // Tahap 4: sama dengan Intake Fisik Garansi Dashboard Gudang (Kamus No. 32): klaim garansi didaftarkan
            // + SPK -GR lama tanpa klaim menurut entry_date. Memanggil fungsi yang sama agar angkanya selalu identik.
            'garansi_masuk' => (int) $intakeFisik['garansi'],
            // Tahap 5: seluruh Sepatu Masuk (berbayar/garansi/rd) mengikuti Intake Fisik Dashboard Gudang.
            'intake_fisik' => [
                'berbayar' => (int) $intakeFisik['berbayar'],
                'garansi' => (int) $intakeFisik['garansi'],
                'rd' => (int) $intakeFisik['rd'],
            ],
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
        foreach (self::otwWsPerHari($start, $end) as $tgl => $n) {
            $data[$tgl]['otw_ws'] = $n;
        }

        $perHari = [];
        for ($d = $start->copy()->startOfDay(); $d->lte($end); $d->addDay()) {
            $baris = ['tanggal' => $d->toDateString()];
            foreach ([...array_values(self::ARUS_GUDANG), 'otw_ws'] as $key) {
                $baris[$key] = $data[$d->toDateString()][$key] ?? 0;
            }
            $perHari[] = $baris;
        }

        return $perHari;
    }

    /**
     * SPK Print OTW WS = SPK unik dalam manifest gudang → workshop (MFST-*, bukan MNF-OUT-*) menurut tanggal manifest
     * dikirim (dispatched_at). workshop_manifest_id ditimpa manifest outbound, jadi isi manifest dilacak dari log
     * OTW_WORKSHOP ("Sent to Workshop Hijau via Manifest #MFST-…") dan ADDED_TO_MANIFEST; SPK yang kemudian
     * dikeluarkan dari manifest yang sama (REMOVED_FROM_MANIFEST) tidak dihitung. Manifest yang dihapus tidak dihitung.
     *
     * @return array<string, int> tanggal => jumlah
     */
    private static function otwWsPerHari(Carbon $start, Carbon $end): array
    {
        $nomor = "SUBSTRING_INDEX(SUBSTRING_INDEX(l.description, '#', -1), ' ', 1)";
        $out = [];
        foreach (DB::select("SELECT DATE(m.dispatched_at) AS tgl, COUNT(DISTINCT l.work_order_id) AS n
                FROM work_order_logs l
                JOIN workshop_manifests m ON m.manifest_number = $nomor
                WHERE ((l.action = 'STATUS_CHANGE' AND l.step = 'OTW_WORKSHOP') OR l.action = 'ADDED_TO_MANIFEST')
                  AND l.description LIKE '%#MFST-%'
                  AND m.deleted_at IS NULL AND m.manifest_number NOT LIKE 'MNF-OUT-%'
                  AND m.dispatched_at BETWEEN ? AND ?
                  AND NOT EXISTS (
                        SELECT 1 FROM work_order_logs r
                        WHERE r.work_order_id = l.work_order_id AND r.action = 'REMOVED_FROM_MANIFEST'
                          AND r.description LIKE CONCAT('%#', m.manifest_number, ' %')
                  )
                GROUP BY DATE(m.dispatched_at)", [
            $start->copy()->startOfDay()->toDateTimeString(), $end->copy()->endOfDay()->toDateTimeString(),
        ]) as $r) {
            $out[$r->tgl] = (int) $r->n;
        }

        return $out;
    }

    /**
     * Sepatu masuk per jenis SPK (basis sama dengan sepatuMasuk()): R&D (priority R&D), garansi (SPK -GR, kecuali
     * yang sekaligus R&D), dan reguler.
     */
    private static function sepatuMasukJenis(Carbon $date): array
    {
        [$s, $e] = self::range($date);
        $gr = WarehouseMomQuery::gr('w');
        $kosong = ['rnd' => 0, 'garansi' => 0, 'reguler' => 0, 'total' => 0];
        $semua = $kosong;
        $sd18 = $kosong;

        foreach (DB::select("SELECT CASE WHEN w.priority = 'R&D' THEN 'rnd' WHEN $gr THEN 'garansi' ELSE 'reguler' END AS jenis,
                    COUNT(*) AS n, COALESCE(SUM(TIME(w.entry_date) < '18:00:00'), 0) AS n18
                FROM work_orders w
                WHERE w.deleted_at IS NULL AND w.status <> 'BATAL' AND w.entry_date BETWEEN ? AND ?
                GROUP BY jenis", [$s, $e]) as $r) {
            $semua[$r->jenis] = (int) $r->n;
            $sd18[$r->jenis] = (int) $r->n18;
        }
        $semua['total'] = $semua['rnd'] + $semua['garansi'] + $semua['reguler'];
        $sd18['total'] = $sd18['rnd'] + $sd18['garansi'] + $sd18['reguler'];

        return $semua + ['sd_18' => $sd18];
    }

    /** Sepatu keluar gudang ke customer = taken_date pada tanggal itu (sama dengan sepatu_keluar_detail.total MoM Gudang). */
    private static function sepatuKeluar(Carbon $date): int
    {
        [$s, $e] = self::range($date);

        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM work_orders
            WHERE deleted_at IS NULL AND taken_date BETWEEN ? AND ?', [$s, $e])->n;
    }

    /**
     * Klaim garansi masuk = klaim dari portal customer (warranty_claims) yang dibuat pada tanggal itu, apa pun statusnya
     * (PENDING/APPROVED/REJECTED). Setiap SPK garansi (work_order_warranties) berasal dari klaim yang disetujui di sini.
     */
    private static function klaimGaransi(Carbon $date): int
    {
        [$s, $e] = self::range($date);

        return (int) DB::selectOne('SELECT COUNT(*) AS n FROM warranty_claims WHERE created_at BETWEEN ? AND ?', [$s, $e])->n;
    }

    /** Log pembatalan (BATAL STATUS_CHANGE/ORDER_CANCELLED) baru tercatat konsisten sejak tanggal ini. */
    private const LOG_BATAL_SEJAK = '2026-07-04';

    /**
     * Cancel = SPK berstatus BATAL (tidak dihapus) yang log pembatalan pertamanya jatuh pada tanggal itu.
     * Sebelum LOG_BATAL_SEJAK tidak ada penanda waktu batal yang bisa dipercaya → null.
     */
    private static function cancel(Carbon $date): ?int
    {
        if ($date->lt(Carbon::parse(self::LOG_BATAL_SEJAK)->startOfDay())) {
            return null;
        }
        [$s, $e] = self::range($date);

        return (int) DB::selectOne("SELECT COUNT(*) AS n FROM (
                SELECT w.id, MIN(l.created_at) AS batal_at
                FROM work_orders w
                JOIN work_order_logs l ON l.work_order_id = w.id
                    AND l.step IN ('BATAL', 'CANCELLED') AND l.action IN ('STATUS_CHANGE', 'ORDER_CANCELLED')
                WHERE w.deleted_at IS NULL AND w.status IN ('BATAL', 'CANCELLED')
                GROUP BY w.id
            ) x WHERE x.batal_at BETWEEN ? AND ?", [$s, $e])->n;
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

    /** Status work order yang tampil di tabel "Butuh Follow Up" modul CX. */
    private const STATUS_BUTUH_FOLLOWUP = "('CX_FOLLOWUP', 'HOLD_FOR_CX')";

    /**
     * Blok Follow Up CX (Tahap 6). masuk/resolve/riwayat menurut tanggal created_at/resolved_at.
     * butuh_followup: hari ini = isi tabel "Butuh Follow Up" saat request (SPK tidak dihapus berstatus CX_FOLLOWUP /
     * HOLD_FOR_CX); tanggal lampau = rumus kpi.open /cx-summary?start=date&end=date (SPK yang kini masih di status itu
     * dan issue-nya dibuat pada tanggal tersebut). Riwayat status work order tidak disimpan, jadi posisi lampau tidak
     * bisa direkonstruksi persis.
     */
    public static function followup(Carbon $date): array
    {
        [$s, $e] = self::range($date);

        if ($date->isSameDay(now())) {
            $butuh = DB::selectOne('SELECT COUNT(*) AS n FROM work_orders
                WHERE deleted_at IS NULL AND status IN '.self::STATUS_BUTUH_FOLLOWUP)->n;
        } else {
            $butuh = DB::selectOne('SELECT COUNT(*) AS n FROM work_orders w
                WHERE w.deleted_at IS NULL AND w.status IN '.self::STATUS_BUTUH_FOLLOWUP.'
                  AND (
                        EXISTS (SELECT 1 FROM cx_issues c WHERE c.work_order_id = w.id AND c.created_at BETWEEN ? AND ?)
                     OR (NOT EXISTS (SELECT 1 FROM cx_issues c WHERE c.work_order_id = w.id) AND w.updated_at BETWEEN ? AND ?)
                  )', [$s, $e, $s, $e])->n;
        }

        $masuk = DB::selectOne('SELECT COUNT(*) AS n FROM cx_issues WHERE created_at BETWEEN ? AND ?', [$s, $e])->n;

        $riwayat = DB::select("SELECT c.spk_number, c.customer_name, c.source, c.category,
                    c.kendala, c.kendala_1, c.description,
                    c.opsi_solusi, c.opsi_solusi_1,
                    c.resolution_type, c.resolution_notes, c.resolution,
                    u.name AS pic, DATE_FORMAT(c.resolved_at, '%H.%i') AS waktu
                FROM cx_issues c
                LEFT JOIN users u ON u.id = c.resolved_by
                WHERE c.status = 'RESOLVED' AND c.resolved_at BETWEEN ? AND ?
                ORDER BY c.resolved_at DESC", [$s, $e]);

        $tipe = array_count_values(array_map(fn ($r) => (string) $r->resolution_type, $riwayat));

        return [
            'butuh_followup' => (int) $butuh,
            'masuk' => (int) $masuk,
            'resolve' => count($riwayat),
            'resolve_tambah_jasa' => (int) ($tipe['tambah_jasa'] ?? 0),
            'resolve_lanjut' => (int) ($tipe['lanjut'] ?? 0),
            'riwayat_resolusi' => array_map(fn ($r) => [
                'spk' => $r->spk_number,
                'customer' => $r->customer_name,
                'source' => $r->source,
                'category' => $r->category,
                'kendala' => self::firstText($r->kendala, $r->kendala_1, $r->description),
                'opsi_solusi' => self::firstText($r->opsi_solusi, $r->opsi_solusi_1),
                'tipe' => $r->resolution_type,
                'catatan' => self::firstText($r->resolution_notes, $r->resolution),
                'pic' => $r->pic,
                'waktu' => $r->waktu,
            ], $riwayat),
        ];
    }

    /** Ambil teks pertama yang tidak kosong setelah trim (membuang spasi/CR/LF berlebih). */
    private static function firstText(?string ...$values): ?string
    {
        foreach ($values as $v) {
            if ($v !== null && ($trimmed = trim($v)) !== '') {
                return $trimmed;
            }
        }

        return null;
    }

    /**
     * Blok Garansi (Inbox Klaim Garansi Mandiri, Tahap 6).
     * pending = klaim yang masih menunggu di akhir hari $date (dibuat ≤ akhir hari dan belum diproses saat itu);
     * untuk hari ini sama dengan tab PENDING. disetujui/ditolak = klaim yang diproses (processed_at) pada tanggal itu.
     */
    public static function garansi(Carbon $date): array
    {
        [$s, $e] = self::range($date);

        $r = DB::selectOne("SELECT
                COALESCE(SUM(created_at <= ? AND (status = 'PENDING' OR processed_at > ?)), 0) AS pending,
                COALESCE(SUM(status = 'APPROVED' AND processed_at BETWEEN ? AND ?), 0) AS disetujui,
                COALESCE(SUM(status = 'REJECTED' AND processed_at BETWEEN ? AND ?), 0) AS ditolak
            FROM warranty_claims", [$e, $e, $s, $e, $s, $e]);

        $out = ['pending' => (int) $r->pending, 'disetujui' => (int) $r->disetujui, 'ditolak' => (int) $r->ditolak];

        return $out + ['total' => $out['pending'] + $out['disetujui'] + $out['ditolak']];
    }

    /**
     * Blok Komplain (kartu ringkasan halaman Keluhan & Janji Temu, Tahap 6): kasus aktif yang dibuat ≤ akhir hari $date,
     * menurut status saat ini (riwayat perubahan status tidak disimpan). Untuk hari ini identik dengan kartu di halaman.
     * total mencakup semua status termasuk REJECTED, sama seperti kartu TOTAL KASUS.
     */
    public static function komplain(Carbon $date): array
    {
        [, $e] = self::range($date);

        $r = DB::selectOne("SELECT COUNT(*) AS total,
                COALESCE(SUM(status = 'PENDING'), 0) AS pending,
                COALESCE(SUM(status = 'PROCESS'), 0) AS diproses,
                COALESCE(SUM(status = 'RESOLVED'), 0) AS selesai
            FROM complaints WHERE deleted_at IS NULL AND created_at <= ?", [$e]);

        return [
            'total' => (int) $r->total,
            'pending' => (int) $r->pending,
            'diproses' => (int) $r->diproses,
            'selesai' => (int) $r->selesai,
        ];
    }

    /** @return array{0: string, 1: string} awal dan akhir hari (WIB). */
    private static function range(Carbon $date): array
    {
        return [$date->copy()->startOfDay()->toDateTimeString(), $date->copy()->endOfDay()->toDateTimeString()];
    }
}
