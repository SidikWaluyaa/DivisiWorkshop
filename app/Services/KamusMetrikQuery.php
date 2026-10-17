<?php

namespace App\Services;

use App\Enums\WorkOrderStatus;
use App\Models\CsLead;
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

    /** SPK closing: SPK dibuat di periode, bukan garansi (-GR) dan bukan R&D (RD-). */
    public static function spkClosing(Carbon $start, Carbon $end): EloquentBuilder
    {
        return WorkOrder::whereBetween('work_orders.created_at', [$start, $end])
            ->whereNot(fn($q) => self::whereGaransi($q, 'work_orders.spk_number'))
            ->where('work_orders.spk_number', 'not like', 'RD-%');
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

        $berbayar = $qcAwal()
            ->whereNot(fn($q) => self::whereGaransi($q))
            ->where('spk_number', 'not like', 'RD-%')
            ->count();

        $rd = $qcAwal()->where('spk_number', 'like', 'RD-%')->count();

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
                spk_number LIKE 'RD-%' as is_rd, taken_date IS NOT NULL as taken, COUNT(*) as n")
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
