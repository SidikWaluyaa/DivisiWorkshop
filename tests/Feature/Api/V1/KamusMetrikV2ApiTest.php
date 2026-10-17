<?php

use App\Enums\WorkOrderStatus;
use App\Models\CsActivity;
use App\Models\CsLead;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

const PERIODE = '?start_date=2026-09-01&end_date=2026-09-25';

beforeEach(function () {
    config(['app.dashboard_api_key' => 'test-secret-key']);
    $this->user = User::factory()->create();
});

/**
 * Insert work order langsung ke tabel agar hook saved (syncFinancials, observer customer) tidak mengubah angka uji.
 */
function insertWorkOrder(array $attributes): int
{
    $row = WorkOrder::factory()->make()->getAttributes();

    return DB::table('work_orders')->insertGetId(array_merge($row, [
        'created_at' => '2026-09-10 10:00:00',
        'updated_at' => '2026-09-10 10:00:00',
    ], $attributes));
}

function insertInvoice(string $number, string $createdAt, array $attributes = []): int
{
    $customer = Customer::firstOrCreate(['phone' => '628111111111'], ['name' => 'Customer Uji']);

    return DB::table('invoices')->insertGetId(array_merge([
        'invoice_number' => $number,
        'customer_id' => $customer->id,
        'total_amount' => 0,
        'paid_amount' => 0,
        'discount' => 0,
        'shipping_cost' => 0,
        'status' => 'Lunas',
        'created_at' => $createdAt,
        'updated_at' => $createdAt,
    ], $attributes));
}

it('finance/dashboard menambah jumlah_tagihan, omset_tagihan, ongkir_tagihan, jumlah_spk_tagihan', function () {
    // A: normal. 1 SPK aktif (jasa 100rb, diskon 10rb) + 1 SPK BATAL + 1 SPK terhapus. Ongkir 15rb.
    $a = insertInvoice('INV-UJI-A', '2026-09-05 09:00:00', ['total_amount' => 100000, 'discount' => 10000, 'shipping_cost' => 15000]);
    insertWorkOrder(['invoice_id' => $a, 'total_service_price' => 100000, 'discount' => 10000, 'total_transaksi' => 90000, 'status' => WorkOrderStatus::PRODUCTION->value]);
    insertWorkOrder(['invoice_id' => $a, 'total_service_price' => 50000, 'status' => WorkOrderStatus::BATAL->value]);
    insertWorkOrder(['invoice_id' => $a, 'total_service_price' => 30000, 'status' => WorkOrderStatus::PRODUCTION->value, 'deleted_at' => '2026-09-06 00:00:00']);

    // B: kasus total_service_price = 0. total_transaksi 95rb = jasa 80rb + ongkir 20rb − diskon 5rb.
    // Jasa bersih yang benar = 75rb (ongkir tidak masuk, diskon dikurangi sekali).
    $b = insertInvoice('INV-UJI-B', '2026-09-12 09:00:00', ['total_amount' => 95000, 'discount' => 5000, 'shipping_cost' => 20000, 'status' => 'DP/Cicil']);
    insertWorkOrder(['invoice_id' => $b, 'total_service_price' => 0, 'total_transaksi' => 95000, 'shipping_cost' => 20000, 'discount' => 5000, 'status' => WorkOrderStatus::SELESAI->value]);

    // C: invoice Batal — tidak dihitung.
    $c = insertInvoice('INV-UJI-C', '2026-09-12 09:00:00', ['status' => 'Batal']);
    insertWorkOrder(['invoice_id' => $c, 'total_service_price' => 40000, 'status' => WorkOrderStatus::SELESAI->value]);

    // D: jasa 0 — tidak dihitung.
    $d = insertInvoice('INV-UJI-D', '2026-09-13 09:00:00');
    insertWorkOrder(['invoice_id' => $d, 'total_service_price' => 0, 'total_transaksi' => 0, 'status' => WorkOrderStatus::SELESAI->value]);

    // E: di luar periode (lewat tengah malam 25 Sep WIB) — tidak dihitung.
    $e = insertInvoice('INV-UJI-E', '2026-09-26 00:00:01');
    insertWorkOrder(['invoice_id' => $e, 'total_service_price' => 60000, 'status' => WorkOrderStatus::SELESAI->value]);

    // F: tepat di batas akhir periode — dihitung.
    $f = insertInvoice('INV-UJI-F', '2026-09-25 23:59:59');
    insertWorkOrder(['invoice_id' => $f, 'total_service_price' => 10000, 'status' => WorkOrderStatus::QC->value]);

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/finance/dashboard' . PERIODE);

    $response->assertOk()
        ->assertJsonPath('data.metrics.jumlah_tagihan', 3)
        ->assertJsonPath('data.metrics.omset_tagihan', 175000)
        ->assertJsonPath('data.metrics.ongkir_tagihan', 35000)
        ->assertJsonPath('data.metrics.jumlah_spk_tagihan', 3)
        // Field lama tidak berubah: rumus lama SUM(total_amount + shipping_cost - discount) untuk semua invoice periode (A, B, C, D, F)
        ->assertJsonPath('data.metrics.total_invoiced_value', 215000)
        ->assertJsonStructure(['data' => ['metrics' => ['total_invoiced_value', 'total_cash_received', 'total_outstanding_receivables', 'collection_rate']]]);
});

it('dashboard-summary menambah total_leads dan total_leads_converted tanpa baris import greeting', function () {
    $makeLead = function (string $status, string $createdAt, ?string $activity = null, bool $deleted = false) {
        $lead = CsLead::create(['customer_phone' => '0812' . random_int(1000000, 9999999), 'status' => $status, 'cs_id' => $this->user->id]);
        DB::table('cs_leads')->where('id', $lead->id)->update([
            'created_at' => $createdAt,
            'deleted_at' => $deleted ? '2026-09-20 00:00:00' : null,
        ]);
        if ($activity) {
            CsActivity::create(['cs_lead_id' => $lead->id, 'user_id' => $this->user->id, 'type' => 'CHAT', 'content' => $activity]);
        }
    };

    // Dihitung: input CS biasa (3, dua di antaranya CONVERTED)
    $makeLead(CsLead::STATUS_CONVERTED, '2026-09-02 10:00:00', 'Lead baru masuk: Tidak ada catatan');
    $makeLead(CsLead::STATUS_CONVERTED, '2026-09-25 23:59:59');
    $makeLead(CsLead::STATUS_KONSULTASI, '2026-09-15 10:00:00');

    // Tidak dihitung: hasil import greeting (tiga varian teks penanda), terhapus, di luar periode
    $makeLead(CsLead::STATUS_GREETING, '2026-09-03 10:00:00', 'Lead diimport ke kolam umum (Greeting).');
    $makeLead(CsLead::STATUS_CONVERTED, '2026-09-03 10:00:00', 'Lead diimport dan langsung dikelola oleh CS Uji');
    $makeLead(CsLead::STATUS_GREETING, '2026-09-03 10:00:00', 'Imported via Excel Greeting Template.');
    $makeLead(CsLead::STATUS_CONVERTED, '2026-09-04 10:00:00', null, true);
    $makeLead(CsLead::STATUS_CONVERTED, '2026-09-26 00:00:01');

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/dashboard-summary' . PERIODE . '&force_refresh=1');

    $response->assertOk()
        ->assertJsonPath('data.summary.total_leads', 3)
        ->assertJsonPath('data.summary.total_leads_converted', 2)
        ->assertJsonStructure(['data' => ['summary' => ['total_closing', 'revenue', 'kalkulasi_closing', 'total_spk_print']]]);
});

it('warehouse-summary menambah field SPK periode non-GR, GR, tiba, dan belum tiba', function () {
    insertWorkOrder(['spk_number' => 'S-UJI-0001-OL', 'warehouse_qc_at' => '2026-09-11 09:00:00', 'status' => WorkOrderStatus::PRODUCTION->value]);
    insertWorkOrder(['spk_number' => 'S-UJI-0002-OL', 'status' => WorkOrderStatus::SPK_PENDING->value]);
    // BATAL sebelum tiba tetap dihitung belum tiba
    insertWorkOrder(['spk_number' => 'S-UJI-0003-OL', 'status' => WorkOrderStatus::BATAL->value, 'created_at' => '2026-09-25 23:59:59']);
    // Garansi: "-GR" dan "-GR-1"
    insertWorkOrder(['spk_number' => 'S-UJI-0004-GR', 'status' => WorkOrderStatus::SELESAI->value]);
    insertWorkOrder(['spk_number' => 'S-UJI-0004-GR-1', 'status' => WorkOrderStatus::SELESAI->value]);
    // Tidak dihitung: terhapus, di luar periode
    insertWorkOrder(['spk_number' => 'S-UJI-0005-OL', 'deleted_at' => '2026-09-12 00:00:00']);
    insertWorkOrder(['spk_number' => 'S-UJI-0006-OL', 'created_at' => '2026-09-26 00:00:01']);

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/warehouse-summary' . PERIODE);

    $response->assertOk()
        ->assertJsonPath('data.summary.spk_periode_non_gr', 3)
        ->assertJsonPath('data.summary.spk_gr_periode', 2)
        ->assertJsonPath('data.summary.spk_tiba_periode', 1)
        ->assertJsonPath('data.summary.spk_belum_tiba_periode', 2)
        ->assertJsonPath('data.summary.pending_reception_period', 1)
        ->assertJsonPath('data.summary.total_incoming', 5);
});
