<?php

namespace App\Services;

use App\Enums\WorkOrderStatus;
use App\Models\CsLead;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Definisi query Kamus Metrik v2.0 yang dipakai bersama oleh dashboard-summary, finance/dashboard,
 * dan warehouse-summary, supaya angka global, per CS, dan harian selalu dari sumber yang sama.
 */
class KamusMetrikQuery
{
    /**
     * Jasa per SPK = total_service_price; bila 0, total_transaksi dikembalikan ke harga jasa
     * (total_transaksi sudah termasuk ongkir SPK dan sudah dikurangi diskon SPK).
     */
    public const JASA_SPK_SQL = 'CASE WHEN work_orders.total_service_price > 0 THEN work_orders.total_service_price
            ELSE GREATEST(COALESCE(work_orders.total_transaksi, 0) - COALESCE(work_orders.shipping_cost, 0) + COALESCE(work_orders.discount, 0), 0) END';

    /** Jasa bersih per SPK: diskon dikurangi satu kali, tanpa ongkir. */
    public const JASA_BERSIH_SPK_SQL = '(' . self::JASA_SPK_SQL . ') - COALESCE(work_orders.discount, 0)';

    /**
     * Lead (data diri customer) per created_at, tanpa baris hasil import greeting Excel.
     * Baris import dikenali dari aktivitas yang selalu ditulis CsGreetingController@import.
     */
    public static function leads(Carbon $start, Carbon $end): EloquentBuilder
    {
        return CsLead::whereBetween('cs_leads.created_at', [$start, $end])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('cs_activities')
                    ->whereColumn('cs_activities.cs_lead_id', 'cs_leads.id')
                    ->where(function ($q) {
                        $q->where('cs_activities.content', 'Imported via Excel Greeting Template.')
                            ->orWhere('cs_activities.content', 'Lead diimport ke kolam umum (Greeting).')
                            ->orWhere('cs_activities.content', 'like', 'Lead diimport dan langsung dikelola oleh %');
                    });
            });
    }

    /** SPK garansi bernomor "...-GR" atau "...-GR-n" (lihat Warranty\Index). */
    public static function whereGaransi($query, string $column = 'spk_number')
    {
        return $query->where($column, 'like', '%-GR')->orWhere($column, 'like', '%-GR-%');
    }

    /**
     * SPK R&D: nomor "RD-..." atau priority 'R&D' (SPK R&D lama bernomor S- saat fitur R&D masih dikembangkan).
     * COALESCE agar priority NULL tidak membuat NOT(...) bernilai NULL dan SPK hilang dari hitungan non-R&D.
     */
    public const RD_SQL = "(%s.spk_number LIKE 'RD-%%' OR COALESCE(%s.priority, '') = 'R&D')";

    public static function rdSql(string $table = 'work_orders'): string
    {
        return sprintf(self::RD_SQL, $table, $table);
    }

    /** SPK R&D yang bukan garansi, supaya berbayar / R&D / garansi tidak beririsan. */
    public static function whereRd($query, string $table = 'work_orders')
    {
        return $query->whereRaw(self::rdSql($table))
            ->whereNot(fn($q) => self::whereGaransi($q, "{$table}.spk_number"));
    }

    /** SPK berbayar: bukan garansi dan bukan R&D. */
    public static function whereBerbayar($query, string $table = 'work_orders')
    {
        return $query->whereNot(fn($q) => self::whereGaransi($q, "{$table}.spk_number"))
            ->whereRaw('NOT ' . self::rdSql($table));
    }

    /** SPK closing: SPK dibuat di periode, bukan garansi (-GR) dan bukan R&D. */
    public static function spkClosing(Carbon $start, Carbon $end): EloquentBuilder
    {
        return self::whereBerbayar(WorkOrder::whereBetween('work_orders.created_at', [$start, $end]));
    }

    /**
     * Invoice valid: created_at di periode, status <> 'Batal', jasa bersih > 0.
     * Satu baris per invoice: id, tanggal, shipping_cost, spk_count, jasa_bersih.
     */
    public static function validInvoices(Carbon $start, Carbon $end): Builder
    {
        $jasaBersihSql = 'SUM(' . self::JASA_SPK_SQL . ') - SUM(COALESCE(work_orders.discount, 0))';

        return self::activeInvoiceSpk(DB::table('invoices'), $start, $end)
            ->groupBy('invoices.id', 'invoices.shipping_cost', 'invoices.created_at')
            ->selectRaw("invoices.id, DATE(invoices.created_at) as tanggal, COALESCE(invoices.shipping_cost, 0) as shipping_cost, COUNT(work_orders.id) as spk_count, {$jasaBersihSql} as jasa_bersih")
            ->havingRaw("{$jasaBersihSql} > 0");
    }

    /**
     * Satu baris per SPK yang menempel di invoice valid: invoice_id, tanggal, work_order_id, cs_id, jasa_bersih.
     * Σ jasa_bersih per invoice = jasa_bersih invoice di validInvoices().
     */
    public static function validInvoiceSpk(Carbon $start, Carbon $end): Builder
    {
        // Id invoice valid diambil dulu: MySQL 5.5 (production) tidak memberi index pada derived table,
        // jadi join langsung ke subquery validInvoices() sangat lambat.
        $validIds = DB::query()->fromSub(self::validInvoices($start, $end), 'valid_invoices')->pluck('id');

        return self::activeInvoiceSpk(DB::table('invoices'), $start, $end)
            ->whereIn('invoices.id', $validIds->all())
            ->selectRaw('invoices.id as invoice_id, DATE(invoices.created_at) as tanggal, work_orders.id as work_order_id, '
                . self::SPK_CS_ID_SQL . ' as cs_id, ' . self::JASA_BERSIH_SPK_SQL . ' as jasa_bersih');
    }

    /**
     * CS sebuah SPK: pemilik lead asal SPK (cs_spk_items → cs_spk → cs_leads.cs_id);
     * bila SPK tidak punya lead, user dengan cs_code yang sama dengan work_orders.cs_code.
     * Subquery berkorelasi (memakai index cs_spk_items.work_order_id), aman untuk MySQL 5.5.
     */
    public const SPK_CS_ID_SQL = 'COALESCE(
            (SELECT MIN(spk_cs_leads.cs_id) FROM cs_spk_items spk_cs_items
                JOIN cs_spk spk_cs_spk ON spk_cs_spk.id = spk_cs_items.spk_id
                JOIN cs_leads spk_cs_leads ON spk_cs_leads.id = spk_cs_spk.cs_lead_id
                WHERE spk_cs_items.work_order_id = work_orders.id),
            (SELECT MIN(spk_cs_users.id) FROM users spk_cs_users
                WHERE spk_cs_users.cs_code = work_orders.cs_code AND spk_cs_users.cs_code <> \'\')
        )';

    /**
     * Kamus No. 32 Intake Fisik, menurut tanggal tiba.
     * - berbayar & rd: tiba = QC awal gudang (warehouse_qc_at, warehouse_qc_status terisi), sama dengan total_qc_processed.
     * - garansi: SPK -GR tidak lewat QC awal dan baru dibuat saat rework selesai, jadi tiba = klaim garansi
     *   didaftarkan (work_order_warranties.created_at); SPK -GR lama tanpa klaim memakai entry_date.
     */
    public static function intakeFisik(Carbon $start, Carbon $end): array
    {
        $qcAwal = fn() => WorkOrder::whereNotNull('warehouse_qc_status')
            ->whereBetween('warehouse_qc_at', [$start, $end]);

        $berbayar = self::whereBerbayar($qcAwal())->count();

        $rd = self::whereRd($qcAwal())->count();

        $klaimGaransi = DB::table('work_order_warranties')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $garansiTanpaKlaim = WorkOrder::where(fn($q) => self::whereGaransi($q))
            ->whereBetween('entry_date', [$start, $end])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('work_order_warranties')
                    ->whereColumn('work_order_warranties.garansi_spk_number', 'work_orders.spk_number');
            })
            ->count();

        return [
            'berbayar' => $berbayar,
            'garansi' => $klaimGaransi + $garansiTanpaKlaim,
            'rd' => $rd,
        ];
    }

    /**
     * Pemetaan status SPK ke tahap WIP. null = di luar alur.
     * Pengecualian ditangani di wipTahapFor(): SELESAI yang sudah diambil, PREPARATION R&D (Stasiun R&D),
     * dan CX_FOLLOWUP/HOLD_FOR_CX yang mengikuti tahap previous_status.
     */
    public const STATUS_TAHAP = [
        'SPK_PENDING' => null,              // belum tiba di gudang
        'DITERIMA' => 'fisik_tiba',
        'ASSESSMENT' => 'sortir',
        'WAITING_PAYMENT' => 'sortir',      // sesudah assessment, sebelum kirim ke workshop
        'WAITING_VERIFICATION' => 'sortir',
        'READY_TO_DISPATCH' => 'sortir',
        'OTW_WORKSHOP' => 'sortir',
        'PREPARATION' => 'sortir',          // R&D: produksi (Stasiun R&D)
        'SORTIR' => 'sortir',
        'PRODUCTION' => 'produksi',
        'REVISI' => 'produksi',             // QC → kembali ke produksi
        'QC' => 'qc_finishing',
        'STAGING_OUTBOUND' => 'qc_finishing', // lolos QC, menunggu manifest ke gudang finish
        'SELESAI' => 'siap_kirim',          // yang sudah diambil (taken_date) di luar alur
        'DIANTAR' => null,                  // sudah keluar untuk diantar
        'CX_FOLLOWUP' => 'previous_status',
        'HOLD_FOR_CX' => 'previous_status',
        'BATAL' => null,
        'DONASI' => null,
        'HISTORY' => null,
    ];

    public const WIP_TAHAP_KEYS = ['fisik_tiba', 'sortir', 'produksi', 'qc_finishing', 'siap_kirim'];

    public static function wipTahapFor(?string $status, ?string $previousStatus, bool $isRd, bool $taken): ?string
    {
        $tahap = self::STATUS_TAHAP[$status] ?? null;

        if ($tahap === 'previous_status') {
            // Ditahan CX: ikut tahap sebelum ditahan; tanpa previous_status = reject QC awal (masih di gudang)
            return $previousStatus ? self::wipTahapFor($previousStatus, null, $isRd, $taken) : 'fisik_tiba';
        }
        if ($status === 'SELESAI' && $taken) {
            return null;
        }
        if ($status === 'PREPARATION' && $isRd) {
            return 'produksi';
        }

        return $tahap;
    }

    /** Snapshot WIP saat ini (tidak mengikuti periode). */
    public static function wipSnapshot(): array
    {
        $rows = WorkOrder::query()
            ->selectRaw("status, CASE WHEN status IN ('CX_FOLLOWUP', 'HOLD_FOR_CX') THEN previous_status END as prev,
                " . self::rdSql() . " as is_rd, taken_date IS NOT NULL as taken, COUNT(*) as n")
            ->groupBy('status', 'prev', 'is_rd', 'taken')
            ->toBase()
            ->get();

        $tahap = array_fill_keys(self::WIP_TAHAP_KEYS, 0);
        $perStatus = array_fill_keys(array_keys(self::STATUS_TAHAP), 0);

        foreach ($rows as $row) {
            $status = (string) $row->status;
            $perStatus[$status] = ($perStatus[$status] ?? 0) + (int) $row->n;

            $key = self::wipTahapFor($status, $row->prev, (bool) $row->is_rd, (bool) $row->taken);
            if ($key !== null) {
                $tahap[$key] += (int) $row->n;
            }
        }

        return ['tahap' => $tahap, 'per_status' => $perStatus];
    }

    /**
     * Status yang sudah melewati gerbang pembayaran (Assessment → WAITING_PAYMENT → READY_TO_DISPATCH)
     * sampai sepatu menunggu diambil. SPK di sini tanpa pembayaran sama sekali berarti sepatu jalan tanpa uang masuk.
     * WAITING_PAYMENT/VERIFICATION tidak termasuk (memang sedang ditahan menunggu bayar).
     */
    public const STATUS_LEWAT_GERBANG_BAYAR = [
        'READY_TO_DISPATCH' => 'Status Ready (siap kirim ke workshop) tapi belum ada DP',
        'OTW_WORKSHOP' => 'Sedang dikirim ke workshop tapi belum ada DP',
        'PREPARATION' => 'Sudah dikerjakan workshop (preparation) tapi belum ada pembayaran',
        'SORTIR' => 'Sudah dikerjakan workshop (sortir) tapi belum ada pembayaran',
        'PRODUCTION' => 'Sudah dikerjakan workshop (produksi) tapi belum ada pembayaran',
        'REVISI' => 'Sudah dikerjakan workshop (revisi) tapi belum ada pembayaran',
        'QC' => 'Sudah dikerjakan workshop (QC) tapi belum ada pembayaran',
        'STAGING_OUTBOUND' => 'Sudah lolos QC, menunggu dikirim ke gudang, tapi belum ada pembayaran',
        'SELESAI' => 'Sudah selesai dan belum diambil, tapi belum ada pembayaran',
    ];

    /**
     * Red alert kebocoran kas (Kamus v2): siap_kirim_belum_dp (posisi saat ini) dan
     * biodata_tanpa_spk (lead periode yang > 1 hari belum punya SPK). Urut dari yang paling lama.
     */
    public static function redAlerts(Carbon $start, Carbon $end, int $limit = 200): array
    {
        $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $tgl = fn(string $date) => (int) substr($date, 8, 2) . ' ' . $bulan[(int) substr($date, 5, 2)];

        // 1) SPK lewat gerbang bayar tanpa pembayaran sama sekali (bukan R&D, bukan garansi, ada nilai jasa)
        $spkRows = self::whereBerbayar(WorkOrder::query())
            ->leftJoin('invoices', 'invoices.id', '=', 'work_orders.invoice_id')
            ->whereIn('work_orders.status', array_keys(self::STATUS_LEWAT_GERBANG_BAYAR))
            ->where(fn($q) => $q->where('work_orders.status', '!=', 'SELESAI')->orWhereNull('work_orders.taken_date'))
            ->where(fn($q) => $q->where('work_orders.total_service_price', '>', 0)->orWhere('work_orders.total_transaksi', '>', 0))
            ->whereRaw('COALESCE(invoices.paid_amount, 0) <= 0')
            ->whereRaw('COALESCE(work_orders.total_paid, 0) <= 0')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('order_payments')
                    ->whereColumn('order_payments.work_order_id', 'work_orders.id')
                    ->where('order_payments.is_verified', true);
            })
            ->selectRaw('work_orders.spk_number, work_orders.customer_name, work_orders.status, '
                . 'DATE(COALESCE(work_orders.entry_date, work_orders.created_at)) as tanggal, '
                . self::SPK_CS_ID_SQL . ' as cs_id')
            ->toBase()
            ->get();

        // 2) Lead periode (definisi total_leads) yang lebih dari 1 hari belum punya SPK CS
        $leadRows = self::leads($start, $end)
            ->where('cs_leads.created_at', '<', now()->subDay())
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('cs_spk')->whereColumn('cs_spk.cs_lead_id', 'cs_leads.id');
            })
            ->selectRaw('cs_leads.id, cs_leads.customer_name, cs_leads.cs_id, DATE(cs_leads.created_at) as tanggal')
            ->toBase()
            ->get();

        $csIds = $spkRows->pluck('cs_id')->merge($leadRows->pluck('cs_id'))->filter()->unique();
        $names = User::withTrashed()->whereIn('id', $csIds)->pluck('name', 'id');

        $items = $spkRows->map(fn($r) => [
            'tipe' => 'siap_kirim_belum_dp',
            'nomor' => $r->spk_number,
            'customer_name' => $r->customer_name,
            'cs_name' => $r->cs_id ? ($names[$r->cs_id] ?? null) : null,
            'masalah' => self::STATUS_LEWAT_GERBANG_BAYAR[$r->status] ?? 'Belum ada pembayaran',
            'tanggal' => $r->tanggal,
        ])->merge($leadRows->map(fn($r) => [
            'tipe' => 'biodata_tanpa_spk',
            'nomor' => 'LEAD-' . $r->id,
            'customer_name' => $r->customer_name,
            'cs_name' => $r->cs_id ? ($names[$r->cs_id] ?? null) : null,
            'masalah' => 'Customer kirim data tgl ' . $tgl($r->tanggal) . ', belum dibuat SPK',
            'tanggal' => $r->tanggal,
        ]));

        return [
            'items' => $items->sortBy([['tanggal', 'asc'], ['nomor', 'asc']])->take($limit)->values()->all(),
            'ringkasan' => [
                'siap_kirim_belum_dp' => $spkRows->count(),
                'biodata_tanpa_spk' => $leadRows->count(),
                'ditampilkan' => min($items->count(), $limit),
            ],
        ];
    }

    private static function activeInvoiceSpk(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query
            ->join('work_orders', 'work_orders.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.created_at', [$start, $end])
            ->where('invoices.status', '!=', 'Batal')
            ->whereNull('work_orders.deleted_at')
            ->whereNotIn('work_orders.status', [WorkOrderStatus::BATAL->value, WorkOrderStatus::DONASI->value]);
    }
}
