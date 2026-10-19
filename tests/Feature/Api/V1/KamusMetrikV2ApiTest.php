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

it('finance/dashboard menambah chart_data.omset_tagihan_harian yang jumlahnya sama dengan omset_tagihan', function () {
    // 2 invoice di 5 Sep (satu kena kasus total_service_price = 0), 1 di 25 Sep 23:59:59, 1 Batal, 1 di luar periode
    $a = insertInvoice('INV-HARIAN-A', '2026-09-05 08:00:00', ['shipping_cost' => 15000]);
    insertWorkOrder(['invoice_id' => $a, 'total_service_price' => 100000, 'discount' => 10000, 'status' => WorkOrderStatus::PRODUCTION->value]);
    $b = insertInvoice('INV-HARIAN-B', '2026-09-05 21:00:00', ['shipping_cost' => 20000]);
    insertWorkOrder(['invoice_id' => $b, 'total_service_price' => 0, 'total_transaksi' => 95000, 'shipping_cost' => 20000, 'discount' => 5000, 'status' => WorkOrderStatus::SELESAI->value]);
    $c = insertInvoice('INV-HARIAN-C', '2026-09-25 23:59:59');
    insertWorkOrder(['invoice_id' => $c, 'total_service_price' => 10000, 'status' => WorkOrderStatus::QC->value]);
    $d = insertInvoice('INV-HARIAN-D', '2026-09-10 10:00:00', ['status' => 'Batal']);
    insertWorkOrder(['invoice_id' => $d, 'total_service_price' => 40000, 'status' => WorkOrderStatus::SELESAI->value]);
    $e = insertInvoice('INV-HARIAN-E', '2026-09-26 00:00:01');
    insertWorkOrder(['invoice_id' => $e, 'total_service_price' => 60000, 'status' => WorkOrderStatus::SELESAI->value]);

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/finance/dashboard' . PERIODE);
    $response->assertOk();

    $harian = $response->json('data.chart_data.omset_tagihan_harian');

    expect($harian)->toHaveCount(25)
        ->and(array_key_first($harian))->toBe('2026-09-01')
        ->and(array_key_last($harian))->toBe('2026-09-25')
        ->and($harian['2026-09-05'])->toEqual(165000)
        ->and($harian['2026-09-25'])->toEqual(10000)
        ->and($harian['2026-09-10'])->toEqual(0)
        ->and(array_sum($harian))->toEqual($response->json('data.metrics.omset_tagihan'))
        ->and($response->json('data.metrics.omset_tagihan'))->toEqual(175000)
        // Field chart lama tetap ada dengan panjang yang sama
        ->and($response->json('data.chart_data.labels'))->toHaveCount(25)
        ->and($response->json('data.chart_data.cash_inflow'))->toHaveCount(25);
});

it('warehouse-summary menambah spk_rd_periode; SPK R&D ikut di spk_periode_non_gr dan spk_tiba_periode', function () {
    insertWorkOrder(['spk_number' => 'RD-2609-10-0001-OL', 'priority' => 'R&D', 'category_spk' => 'RD', 'warehouse_qc_at' => '2026-09-11 09:00:00', 'status' => WorkOrderStatus::PREPARATION->value]);
    insertWorkOrder(['spk_number' => 'RD-2609-12-0002-OL', 'priority' => 'R&D', 'status' => WorkOrderStatus::SPK_PENDING->value]);
    insertWorkOrder(['spk_number' => 'S-2609-12-0003-OL', 'warehouse_qc_at' => '2026-09-12 09:00:00', 'status' => WorkOrderStatus::PRODUCTION->value]);
    // Tidak dihitung: terhapus, di luar periode
    insertWorkOrder(['spk_number' => 'RD-2609-13-0004-OL', 'deleted_at' => '2026-09-14 00:00:00']);
    insertWorkOrder(['spk_number' => 'RD-2609-26-0005-OL', 'created_at' => '2026-09-26 00:00:01']);

    $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/warehouse-summary' . PERIODE)
        ->assertOk()
        ->assertJsonPath('data.summary.spk_rd_periode', 2)
        ->assertJsonPath('data.summary.total_incoming', 3)
        ->assertJsonPath('data.summary.spk_periode_non_gr', 3)
        ->assertJsonPath('data.summary.spk_tiba_periode', 2);
});

it('dashboard-summary menambah per_cs_kamus dan harian_kamus yang jumlahnya sama dengan angka global', function () {
    $csA = User::factory()->create(['name' => 'CS A', 'role' => 'cs', 'cs_code' => 'AAA']);
    $csB = User::factory()->create(['name' => 'CS B', 'role' => 'cs', 'cs_code' => 'BBB']);
    $csC = User::factory()->create(['name' => 'CS C', 'role' => 'cs', 'cs_code' => 'CCC']); // tanpa data
    $customer = Customer::firstOrCreate(['phone' => '628111111111'], ['name' => 'Customer Uji']);

    $lead = function (User $cs, string $createdAt, ?string $activity = null, bool $deleted = false) {
        $lead = CsLead::create(['customer_phone' => '0812' . random_int(1000000, 9999999), 'status' => CsLead::STATUS_CONVERTED, 'cs_id' => $cs->id]);
        DB::table('cs_leads')->where('id', $lead->id)->update(['created_at' => $createdAt, 'deleted_at' => $deleted ? '2026-09-20 00:00:00' : null]);
        if ($activity) {
            CsActivity::create(['cs_lead_id' => $lead->id, 'user_id' => $cs->id, 'type' => 'CHAT', 'content' => $activity]);
        }
        return $lead->id;
    };
    $linkSpk = function (int $leadId, int $workOrderId) use ($customer) {
        $spkId = DB::table('cs_spk')->insertGetId(['cs_lead_id' => $leadId, 'spk_number' => 'SPK-CS-' . $workOrderId, 'customer_id' => $customer->id, 'services' => '[]', 'total_price' => 0]);
        DB::table('cs_spk_items')->insert(['spk_id' => $spkId, 'work_order_id' => $workOrderId]);
    };

    // Leads: A = 2, B = 1. Tidak dihitung: import greeting, terhapus, di luar periode.
    $leadA = $lead($csA, '2026-09-02 10:00:00');
    $lead($csA, '2026-09-03 10:00:00');
    $leadB = $lead($csB, '2026-09-03 11:00:00');
    $lead($csA, '2026-09-03 12:00:00', 'Lead diimport ke kolam umum (Greeting).');
    $lead($csB, '2026-09-04 10:00:00', null, true);
    $lead($csA, '2026-09-26 00:00:01');

    // SPK closing. CS dari lead asal SPK; fallback cs_code; selain itu Tanpa CS.
    $w1 = insertWorkOrder(['spk_number' => 'S-2609-02-0001-AAA', 'cs_code' => 'AAA', 'created_at' => '2026-09-02 12:00:00', 'total_service_price' => 100000, 'discount' => 10000]);
    $w2 = insertWorkOrder(['spk_number' => 'S-2609-03-0002-BBB', 'cs_code' => 'BBB', 'created_at' => '2026-09-03 12:00:00', 'total_service_price' => 200000]);
    $w3 = insertWorkOrder(['spk_number' => 'S-2609-03-0003-BBB', 'cs_code' => 'BBB', 'created_at' => '2026-09-03 13:00:00', 'total_service_price' => 0, 'total_transaksi' => 95000, 'shipping_cost' => 20000, 'discount' => 5000]);
    $w4 = insertWorkOrder(['spk_number' => 'S-2609-04-0004-ZZZ', 'cs_code' => 'ZZZ', 'created_at' => '2026-09-04 12:00:00', 'total_service_price' => 50000]);
    $w5 = insertWorkOrder(['spk_number' => 'S-2609-04-0005-GR', 'created_at' => '2026-09-04 13:00:00', 'total_service_price' => 40000]);
    $w6 = insertWorkOrder(['spk_number' => 'RD-2609-04-0006-AAA', 'cs_code' => 'AAA', 'created_at' => '2026-09-04 14:00:00', 'total_service_price' => 30000]);
    insertWorkOrder(['spk_number' => 'S-2609-04-0007-AAA', 'cs_code' => 'AAA', 'created_at' => '2026-09-04 15:00:00', 'deleted_at' => '2026-09-05 00:00:00']);
    $w8 = insertWorkOrder(['spk_number' => 'S-2609-05-0008-AAA', 'cs_code' => 'BBB', 'created_at' => '2026-09-05 12:00:00', 'status' => WorkOrderStatus::BATAL->value, 'total_service_price' => 70000]);
    $linkSpk($leadA, $w1);
    $linkSpk($leadB, $w2);
    $linkSpk($leadA, $w8); // lead menang atas cs_code

    // Invoice: INV1 berisi SPK dua CS (A 90rb, B 200rb) → jumlah_tagihan ke B
    $inv = fn($no, $at, $status = 'Lunas') => insertInvoice($no, $at, ['status' => $status]);
    $i1 = $inv('INV-CS-1', '2026-09-05 09:00:00');
    $i2 = $inv('INV-CS-2', '2026-09-05 10:00:00');
    $i3 = $inv('INV-CS-3', '2026-09-10 10:00:00');
    $i4 = $inv('INV-CS-4', '2026-09-06 10:00:00', 'Batal');
    $i5 = $inv('INV-CS-5', '2026-09-06 11:00:00');
    $i6 = $inv('INV-CS-6', '2026-09-06 12:00:00');
    foreach ([$w1 => $i1, $w2 => $i1, $w3 => $i2, $w4 => $i3, $w5 => $i4, $w8 => $i5, $w6 => $i6] as $wo => $invoiceId) {
        DB::table('work_orders')->where('id', $wo)->update(['invoice_id' => $invoiceId]);
    }

    $headers = ['X-API-KEY' => 'test-secret-key'];
    $summary = $this->withHeaders($headers)->getJson('/api/v1/dashboard-summary' . PERIODE . '&force_refresh=1')->assertOk();
    $finance = $this->withHeaders($headers)->getJson('/api/v1/finance/dashboard' . PERIODE)->assertOk();
    $warehouse = $this->withHeaders($headers)->getJson('/api/v1/warehouse-summary' . PERIODE)->assertOk();

    $perCs = collect($summary->json('data.per_cs_kamus'))->keyBy(fn($r) => $r['cs_name']);
    $harian = $summary->json('data.harian_kamus');

    $global = [
        'leads' => $summary->json('data.summary.total_leads'),
        'spk_closing' => $warehouse->json('data.summary.spk_periode_non_gr') - $warehouse->json('data.summary.spk_rd_periode'),
        'jumlah_tagihan' => $finance->json('data.metrics.jumlah_tagihan'),
        'jumlah_spk_tagihan' => $finance->json('data.metrics.jumlah_spk_tagihan'),
        'omset_tagihan' => $finance->json('data.metrics.omset_tagihan'),
    ];
    expect($global)->toEqual(['leads' => 3, 'spk_closing' => 5, 'jumlah_tagihan' => 4, 'jumlah_spk_tagihan' => 5, 'omset_tagihan' => 445000]);

    // Σ per CS = global, Σ harian = global
    foreach ($global as $field => $value) {
        expect($perCs->sum($field))->toEqual($value, "per_cs_kamus {$field}")
            ->and(collect($harian)->sum($field))->toEqual($value, "harian_kamus {$field}");
    }

    expect($perCs['CS A'])->toMatchArray(['cs_id' => $csA->id, 'leads' => 2, 'spk_closing' => 2, 'jumlah_tagihan' => 1, 'jumlah_spk_tagihan' => 2, 'omset_tagihan' => 120000])
        ->and($perCs['CS B'])->toMatchArray(['cs_id' => $csB->id, 'leads' => 1, 'spk_closing' => 2, 'jumlah_tagihan' => 2, 'jumlah_spk_tagihan' => 2, 'omset_tagihan' => 275000])
        ->and($perCs['CS C'])->toMatchArray(['leads' => 0, 'spk_closing' => 0, 'jumlah_tagihan' => 0, 'omset_tagihan' => 0])
        ->and($perCs['Tanpa CS'])->toMatchArray(['cs_id' => null, 'leads' => 0, 'spk_closing' => 1, 'jumlah_tagihan' => 1, 'jumlah_spk_tagihan' => 1, 'omset_tagihan' => 50000])
        ->and(last($summary->json('data.per_cs_kamus'))['cs_name'])->toBe('Tanpa CS');

    // Setiap tanggal ada; tanggal kosong = 0; omset harian = finance chart_data.omset_tagihan_harian
    expect($harian)->toHaveCount(25)
        ->and($harian['2026-09-07'])->toEqual(['leads' => 0, 'spk_closing' => 0, 'jumlah_tagihan' => 0, 'jumlah_spk_tagihan' => 0, 'omset_tagihan' => 0])
        ->and($harian['2026-09-03'])->toMatchArray(['leads' => 2, 'spk_closing' => 2])
        ->and($harian['2026-09-05'])->toMatchArray(['spk_closing' => 1, 'jumlah_tagihan' => 2, 'jumlah_spk_tagihan' => 3, 'omset_tagihan' => 365000])
        ->and(collect($harian)->map(fn($d) => $d['omset_tagihan'])->all())->toEqual($finance->json('data.chart_data.omset_tagihan_harian'));

    // per_cs lama tetap ada dengan bentuk lama
    $summary->assertJsonStructure(['data' => ['per_cs' => [['cs_id', 'cs_name', 'total_closing', 'revenue', 'avg_deal']]]]);
});

it('warehouse-summary menambah intake_fisik_berbayar, intake_fisik_garansi, intake_fisik_rd menurut tanggal tiba', function () {
    $qc = fn(string $spk, string $at, array $extra = []) => insertWorkOrder(array_merge(
        ['spk_number' => $spk, 'warehouse_qc_status' => 'lolos', 'warehouse_qc_at' => $at, 'status' => WorkOrderStatus::PRODUCTION->value], $extra));

    // Berbayar 2, R&D 1 (tiba = QC awal). Tidak dihitung: terhapus, QC di luar periode, warehouse_qc_status kosong.
    $asal = $qc('S-2609-01-0001-OL', '2026-09-02 10:00:00');
    $qc('S-2609-01-0002-OL', '2026-09-25 23:59:59');
    $qc('RD-2609-01-0003-OL', '2026-09-03 10:00:00');
    $qc('S-2609-01-0004-OL', '2026-09-04 10:00:00', ['deleted_at' => '2026-09-05 00:00:00']);
    $qc('S-2609-01-0005-OL', '2026-09-26 00:00:01');
    insertWorkOrder(['spk_number' => 'S-2609-01-0006-OL', 'warehouse_qc_at' => '2026-09-06 10:00:00']);

    // Garansi: 2 klaim di periode (satu sudah punya WO -GR, satu belum) + 1 SPK -GR lama tanpa klaim. Klaim di luar periode tidak dihitung.
    $klaim = fn(string $grSpk, string $at) => DB::table('work_order_warranties')->insert([
        'work_order_id' => $asal, 'garansi_spk_number' => $grSpk, 'description' => 'uji', 'created_by' => $this->user->id,
        'status' => 'OPEN', 'created_at' => $at, 'updated_at' => $at,
    ]);
    $klaim('S-2609-01-0001-GR', '2026-09-07 10:00:00');
    insertWorkOrder(['spk_number' => 'S-2609-01-0001-GR', 'entry_date' => '2026-09-07 10:00:00', 'status' => WorkOrderStatus::SELESAI->value]);
    $klaim('S-2609-01-0002-GR', '2026-09-08 10:00:00');
    $klaim('S-2608-01-0009-GR', '2026-08-30 10:00:00');
    insertWorkOrder(['spk_number' => 'S-2604-01-0010-GR-1', 'entry_date' => '2026-09-09 10:00:00', 'status' => WorkOrderStatus::SELESAI->value]);

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/warehouse-summary' . PERIODE)->assertOk();

    $response->assertJsonPath('data.summary.intake_fisik_berbayar', 2)
        ->assertJsonPath('data.summary.intake_fisik_rd', 1)
        ->assertJsonPath('data.summary.intake_fisik_garansi', 3)
        ->assertJsonPath('data.summary.total_qc_processed', 3);

    expect($response->json('data.summary.intake_fisik_berbayar') + $response->json('data.summary.intake_fisik_rd'))
        ->toBe($response->json('data.summary.total_qc_processed'));
});

it('setiap WorkOrderStatus punya pemetaan tahap WIP', function () {
    foreach (WorkOrderStatus::cases() as $case) {
        expect(array_key_exists($case->value, \App\Services\KamusMetrikQuery::STATUS_TAHAP))->toBeTrue("status {$case->value} belum dipetakan");
    }
});

it('warehouse-summary menambah wip_tahap dan wip_per_status (snapshot)', function () {
    $wo = fn(string $status, array $extra = []) => insertWorkOrder(array_merge(['status' => $status], $extra));

    $wo('SPK_PENDING');                                   // di luar alur
    $wo('DITERIMA');                                      // fisik_tiba
    $wo('CX_FOLLOWUP');                                   // reject QC awal → fisik_tiba
    $wo('ASSESSMENT');                                    // sortir
    $wo('WAITING_PAYMENT');                               // sortir
    $wo('OTW_WORKSHOP');                                  // sortir
    $wo('PREPARATION', ['spk_number' => 'S-2609-01-0101-OL']);  // sortir
    $wo('SORTIR');                                        // sortir
    $wo('CX_FOLLOWUP', ['previous_status' => 'SORTIR']);  // sortir
    $wo('PREPARATION', ['spk_number' => 'RD-2609-01-0102-OL']); // produksi (Stasiun R&D)
    $wo('PRODUCTION');                                    // produksi
    $wo('REVISI');                                        // produksi
    $wo('CX_FOLLOWUP', ['previous_status' => 'PRODUCTION']); // produksi
    $wo('QC');                                            // qc_finishing
    $wo('STAGING_OUTBOUND');                              // qc_finishing
    $wo('SELESAI');                                       // siap_kirim
    $wo('SELESAI', ['taken_date' => '2026-09-10 10:00:00']); // di luar alur (sudah diambil)
    $wo('BATAL');                                         // di luar alur
    $wo('DONASI');                                        // di luar alur
    $wo('PRODUCTION', ['deleted_at' => '2026-09-10 00:00:00']); // terhapus, tidak dihitung

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/warehouse-summary' . PERIODE)->assertOk();

    expect($response->json('data.summary.wip_tahap'))->toBe([
        'fisik_tiba' => 2, 'sortir' => 6, 'produksi' => 4, 'qc_finishing' => 2, 'siap_kirim' => 1,
    ]);

    $perStatus = $response->json('data.summary.wip_per_status');
    expect($perStatus)->toHaveKeys(array_map(fn($c) => $c->value, WorkOrderStatus::cases()))
        ->and($perStatus['CX_FOLLOWUP'])->toBe(3)
        ->and($perStatus['SELESAI'])->toBe(2)
        ->and($perStatus['PRODUCTION'])->toBe(1)
        ->and($perStatus['DIANTAR'])->toBe(0)
        ->and(array_sum($perStatus))->toBe(19);
});

it('warehouse-summary menambah siap_kirim_aktif: siap_kirim yang selesai dalam 30 hari terakhir', function () {
    Carbon\Carbon::setTestNow('2026-10-17 12:00:00');

    $selesai = fn(array $extra) => insertWorkOrder(array_merge(['status' => WorkOrderStatus::SELESAI->value], $extra));
    $selesai(['finished_date' => '2026-10-16 09:00:00']);                                   // aktif
    $selesai(['finished_date' => '2026-09-17 12:00:01']);                                   // aktif (masih dalam 30 hari)
    $selesai(['finished_date' => '2026-09-17 11:59:59']);                                   // backlog lama
    $selesai(['finished_date' => null]);                                                    // tanpa tanggal selesai
    $selesai(['finished_date' => '2026-10-15 09:00:00', 'taken_date' => '2026-10-16 09:00:00']); // sudah diambil
    $selesai(['finished_date' => '2026-10-15 09:00:00', 'deleted_at' => '2026-10-16 00:00:00']); // terhapus
    insertWorkOrder(['status' => WorkOrderStatus::QC->value, 'finished_date' => '2026-10-15 09:00:00']); // bukan SELESAI

    $response = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson('/api/v1/warehouse-summary' . PERIODE)->assertOk();

    $response->assertJsonPath('data.summary.siap_kirim_aktif', 2)
        ->assertJsonPath('data.summary.wip_tahap.siap_kirim', 4);

    Carbon\Carbon::setTestNow();
});

it('penanda R&D = nomor RD- ATAU priority R&D, konsisten dan tanpa hitung ganda', function () {
    $qc = ['warehouse_qc_status' => 'lolos', 'warehouse_qc_at' => '2026-09-12 10:00:00', 'status' => WorkOrderStatus::PRODUCTION->value];

    insertWorkOrder(array_merge($qc, ['spk_number' => 'RD-2609-01-0001-OL', 'priority' => 'R&D']));   // R&D
    insertWorkOrder(array_merge($qc, ['spk_number' => 'S-2609-01-0002-FR', 'priority' => 'R&D']));    // R&D lama bernomor S-
    insertWorkOrder(array_merge($qc, ['spk_number' => 'S-2609-01-0003-OL', 'priority' => 'Reguler'])); // berbayar
    insertWorkOrder(array_merge($qc, ['spk_number' => 'S-2609-01-0004-OL', 'priority' => '']));        // berbayar (priority kosong)
    insertWorkOrder(['spk_number' => 'S-2609-01-0005-GR', 'priority' => 'R&D']);                      // garansi, bukan R&D
    insertWorkOrder(['spk_number' => 'S-2609-01-0006-OL', 'priority' => 'R&D', 'status' => WorkOrderStatus::PREPARATION->value]); // R&D, belum tiba

    $headers = ['X-API-KEY' => 'test-secret-key'];
    $w = $this->withHeaders($headers)->getJson('/api/v1/warehouse-summary' . PERIODE)->assertOk()->json('data.summary');
    $d = $this->withHeaders($headers)->getJson('/api/v1/dashboard-summary' . PERIODE . '&force_refresh=1')->assertOk()->json('data');

    expect($w['spk_rd_periode'])->toBe(3)
        ->and($w['spk_periode_non_gr'])->toBe(5)
        ->and($w['spk_gr_periode'])->toBe(1)
        ->and($w['intake_fisik_rd'])->toBe(2)
        ->and($w['intake_fisik_berbayar'])->toBe(2)
        // berbayar + rd = total_qc_processed, tanpa irisan
        ->and($w['intake_fisik_berbayar'] + $w['intake_fisik_rd'])->toBe($w['total_qc_processed'])
        // spk_closing = non_gr − rd, sama di per_cs_kamus dan harian_kamus
        ->and(collect($d['per_cs_kamus'])->sum('spk_closing'))->toBe($w['spk_periode_non_gr'] - $w['spk_rd_periode'])
        ->and(collect($d['harian_kamus'])->sum('spk_closing'))->toBe(2)
        // PREPARATION R&D bernomor S- masuk produksi (Stasiun R&D)
        ->and($w['wip_tahap']['produksi'])->toBe(5); // 4 PRODUCTION + 1 PREPARATION R&D bernomor S-
});
