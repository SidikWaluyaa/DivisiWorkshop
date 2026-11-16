<?php

use App\Models\Customer;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/*
 * Kamus MoM Gudang (Tahap 2): blok as-of di /warehouse-summary.
 * Skenario dihitung manual per as_of = 2026-10-31 23:59:59, periode 1–31 Okt 2026.
 */

const MOM_URL = '/api/v1/warehouse-summary?start_date=2026-10-01&end_date=2026-10-31';

beforeEach(function () {
    config(['app.dashboard_api_key' => 'test-secret-key']);
    Cache::flush();
    $this->user = User::factory()->create();
});

function momWo(array $attributes): int
{
    $row = WorkOrder::factory()->make()->getAttributes();

    return DB::table('work_orders')->insertGetId(array_merge($row, [
        'invoice_id' => null,
        'finished_date' => null,
        'taken_date' => null,
        'warehouse_qc_at' => null,
        'priority' => 'Regular',
        'deleted_at' => null,
    ], $attributes));
}

function momLog(int $woId, string $step, string $action, string $at): void
{
    DB::table('work_order_logs')->insert([
        'work_order_id' => $woId, 'step' => $step, 'action' => $action,
        'description' => 'uji', 'created_at' => $at, 'updated_at' => $at,
    ]);
}

function momInvoice(string $number, float $total): int
{
    $customer = Customer::firstOrCreate(['phone' => '628122222222'], ['name' => 'Customer Uji MoM']);

    return DB::table('invoices')->insertGetId([
        'invoice_number' => $number, 'customer_id' => $customer->id,
        'total_amount' => $total, 'paid_amount' => $total, 'discount' => 0, 'shipping_cost' => 0,
        'status' => 'Lunas', 'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00',
    ]);
}

function momPayment(int $invoiceId, int $amount, string $paidAt): void
{
    DB::table('order_payments')->insert([
        'invoice_id' => $invoiceId, 'amount_total' => $amount, 'payment_method' => 'transfer',
        'paid_at' => $paidAt, 'is_verified' => 1, 'created_at' => $paidAt, 'updated_at' => $paidAt,
    ]);
}

function momAssignment(int $woId, string $category, string $createdAt, ?string $retrievedAt): void
{
    DB::table('storage_assignments')->insert([
        'work_order_id' => $woId, 'rack_code' => 'UJI-'.strtoupper($category), 'category' => $category, 'item_type' => $category,
        // stored_at sengaja = retrieved_at: meniru kolom ON UPDATE CURRENT_TIMESTAMP di production.
        'stored_at' => $retrievedAt ?? $createdAt,
        'retrieved_at' => $retrievedAt,
        'status' => $retrievedAt ? 'retrieved' : 'stored',
        'created_at' => $createdAt, 'updated_at' => $retrievedAt ?? $createdAt,
    ]);
}

function momSkenario(): void
{
    // A: terbit 2 Okt, tiba 3 Okt (1 hari), dikirim ke workshop 4 Okt (log ganda), SELESAI 20 Okt,
    //    masuk rak sepatu 21 Okt, diambil 5 Nov → per 31 Okt: ready_for_pickup & di rak.
    $a = momWo(['spk_number' => 'UJI-MOM-A', 'status' => 'SELESAI', 'created_at' => '2026-10-02 10:00:00', 'updated_at' => '2026-11-05 10:00:00',
        'finished_date' => '2026-10-20 10:00:00', 'taken_date' => '2026-11-05 10:00:00']);
    momLog($a, 'DITERIMA', 'STATUS_CHANGE', '2026-10-03 10:00:00');
    momLog($a, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-10-04 09:00:00');
    momLog($a, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-10-04 09:00:30');
    momLog($a, 'SELESAI', 'STATUS_CHANGE', '2026-10-20 10:00:00');
    momAssignment($a, 'before', '2026-10-03 10:00:00', '2026-10-05 10:00:00');   // 48 jam
    momAssignment($a, 'shoes', '2026-10-21 10:00:00', '2026-11-05 10:00:00');

    // B: pending, terbit 25 Okt (≤ 7 hari pada 31 Okt). C: pending, terbit 5 Okt (> 7 hari). P: pending sejak Agustus (> 30 hari).
    momWo(['spk_number' => 'UJI-MOM-B', 'status' => 'SPK_PENDING', 'created_at' => '2026-10-25 10:00:00', 'updated_at' => '2026-10-25 10:00:00']);
    momWo(['spk_number' => 'UJI-MOM-C', 'status' => 'SPK_PENDING', 'created_at' => '2026-10-05 10:00:00', 'updated_at' => '2026-10-05 10:00:00']);
    momWo(['spk_number' => 'UJI-MOM-P', 'status' => 'SPK_PENDING', 'created_at' => '2026-08-01 10:00:00', 'updated_at' => '2026-08-01 10:00:00']);

    // D: SPK garansi dibuat langsung SELESAI tanpa log dan tanpa finished_date (syncToFinish) → finished_not_stored.
    momWo(['spk_number' => 'UJI-MOM-D-GR', 'status' => 'SELESAI', 'created_at' => '2026-10-10 10:00:00', 'updated_at' => '2026-10-10 10:00:00']);

    // E & F satu invoice (100rb). 60rb dibayar 1 Okt, 40rb dibayar 2 Nov.
    // E keluar 15 Okt (belum lunas saat keluar), F keluar 3 Nov → invoice dihitung di Oktober (keluar pertama).
    $i1 = momInvoice('INV-UJI-MOM-1', 100000);
    momPayment($i1, 60000, '2026-10-01 09:00:00');
    momPayment($i1, 40000, '2026-11-02 09:00:00');
    $e = momWo(['spk_number' => 'UJI-MOM-E', 'status' => 'SELESAI', 'invoice_id' => $i1, 'created_at' => '2026-09-05 10:00:00', 'updated_at' => '2026-10-15 10:00:00',
        'finished_date' => '2026-09-20 10:00:00', 'taken_date' => '2026-10-15 10:00:00']);
    momLog($e, 'SELESAI', 'STATUS_CHANGE', '2026-09-20 10:00:00');
    $f = momWo(['spk_number' => 'UJI-MOM-F', 'status' => 'SELESAI', 'invoice_id' => $i1, 'created_at' => '2026-09-05 10:00:00', 'updated_at' => '2026-11-03 10:00:00',
        'finished_date' => '2026-09-20 10:00:00', 'taken_date' => '2026-11-03 10:00:00']);
    momLog($f, 'SELESAI', 'STATUS_CHANGE', '2026-09-20 10:00:00');

    // G: SPK garansi keluar 20 Okt tanpa invoice.
    momWo(['spk_number' => 'UJI-MOM-G-GR', 'status' => 'SELESAI', 'created_at' => '2026-09-10 10:00:00', 'updated_at' => '2026-10-20 10:00:00',
        'finished_date' => '2026-10-19 10:00:00', 'taken_date' => '2026-10-20 10:00:00']);

    // H: invoice 50rb lunas sebelum keluar 21 Okt. Sepatu di rak 10 Okt → 21 Okt (264 jam).
    $i2 = momInvoice('INV-UJI-MOM-2', 50000);
    momPayment($i2, 50000, '2026-10-01 09:00:00');
    $h = momWo(['spk_number' => 'UJI-MOM-H', 'status' => 'SELESAI', 'invoice_id' => $i2, 'created_at' => '2026-09-05 10:00:00', 'updated_at' => '2026-10-21 10:00:00',
        'finished_date' => '2026-10-09 10:00:00', 'taken_date' => '2026-10-21 10:00:00']);
    momLog($h, 'SELESAI', 'STATUS_CHANGE', '2026-10-09 10:00:00');
    momAssignment($h, 'shoes', '2026-10-10 10:00:00', '2026-10-21 10:00:00');

    // Barang manual aktif, rak, manifest dua arah.
    DB::table('manual_storage_items')->insert(['item_name' => 'Barang uji', 'rack_code' => 'UJI-MANUAL', 'in_date' => '2026-10-01 10:00:00',
        'stored_by' => test()->user->id, 'status' => 'stored', 'created_at' => '2026-10-01 10:00:00', 'updated_at' => '2026-10-01 10:00:00']);
    DB::table('storage_racks')->insert(['rack_code' => 'UJI-SHOES', 'category' => 'shoes', 'capacity' => 10, 'current_count' => 0, 'status' => 'active',
        'created_at' => '2026-09-01 10:00:00', 'updated_at' => '2026-09-01 10:00:00']);
    DB::table('workshop_manifests')->insert([
        ['manifest_number' => 'MNF-UJI-IN-1', 'status' => 'RECEIVED', 'dispatched_at' => '2026-10-04 09:00:00', 'created_at' => '2026-10-04 09:00:00', 'updated_at' => '2026-10-04 09:00:00'],
        ['manifest_number' => 'MNF-OUT-UJI-1', 'status' => 'SENT', 'dispatched_at' => '2026-10-19 09:00:00', 'created_at' => '2026-10-19 09:00:00', 'updated_at' => '2026-10-19 09:00:00'],
    ]);
}

it('tanpa as_of_date tidak menambah blok baru', function () {
    $data = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(MOM_URL)->assertOk()->json('data');

    expect(array_keys($data))->toBe(['summary', 'qc_analytics', 'efficiency', 'inventory', 'storage', 'queues', 'period', 'metadata']);
});

it('as_of_date yang tidak valid ditolak 422', function (string $value) {
    $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(MOM_URL.'&as_of_date='.$value)->assertStatus(422);
})->with(['2026-13-01', '2026-02-30', '31-10-2026', '2099-01-01']);

it('menghitung posisi, sepatu keluar, kedatangan, efisiensi, dan pengiriman per as_of_date', function () {
    momSkenario();

    $data = $this->withHeader('X-API-KEY', 'test-secret-key')
        ->getJson(MOM_URL.'&as_of_date=2026-10-31')->assertOk()->json('data');

    expect($data['posisi_as_of'])->toMatchArray([
        'as_of' => '2026-10-31 23:59:59',
        'pending_reception_total' => 3,
        'pending_reception_le_30_hari' => 2,
        'pending_reception_gt_30_hari' => 1,
        'siap_kirim_aktif' => 1,          // A (finished 20 Okt); F & D di luar 30 hari / tanpa finished_date
        'finished_not_stored' => 2,       // D & F
        'ready_for_pickup' => 1,          // A
        'ready_for_pickup_sepatu' => 1,   // A (assignment shoes aktif per 31 Okt)
        'shoes_in_rack_count' => 1,       // A (dari created_at, walau stored_at tertimpa 5 Nov)
        'donation_candidates_count' => 0,
        'inbound_inventory' => 0,
        'manual_items_aktif' => 1,
        'accessories_aktif' => 0,
        'finish_inventory' => 2,
        'stored_items' => 2,
        'total_capacity' => 10,
        'shipping_pending' => 0,
        'wip_tahap' => ['fisik_tiba' => 0, 'sortir' => 0, 'produksi' => 0, 'qc_finishing' => 0, 'siap_kirim' => 3],
    ]);
    expect($data['posisi_as_of']['metode'])->toHaveKeys(['shipping_pending', 'total_capacity']);

    $keluar = $data['sepatu_keluar_detail'];
    expect($keluar)->toMatchArray([
        'pasang_total' => 3,                      // E, G, H
        'pasang_lunas_saat_keluar' => 1,          // H
        'pasang_belum_lunas_saat_keluar' => 1,    // E (lunas 2 Nov)
        'pasang_tanpa_invoice' => 1,              // G
        'pasang_garansi' => 1,
        'invoice_count' => 2,
        'rupiah_tagihan' => 150000,
        'rupiah_dibayar_saat_keluar' => 110000,
        'rupiah_sisa_saat_keluar' => 40000,
        'invoice_belum_lunas_saat_keluar' => 1,
        'aturan_atribusi_invoice' => 'keluar_pertama',
    ]);
    expect($keluar['pasang_lunas_saat_keluar'] + $keluar['pasang_belum_lunas_saat_keluar'] + $keluar['pasang_tanpa_invoice'])
        ->toBe($keluar['pasang_total']);

    $datang = $data['kedatangan'];
    expect($datang)->toMatchArray([
        'spk_terbit_non_gr' => 3,     // A, B, C (D garansi tidak dihitung)
        'sudah_tiba' => 1,
        'belum_tiba_le_7_hari' => 1,
        'belum_tiba_gt_7_hari' => 1,
        'batal' => 0,
        'hari_tiba' => ['median' => 1, 'p80' => 1, 'p90' => 1, 'n' => 1],
    ]);
    expect($datang['sudah_tiba'] + $datang['belum_tiba_le_7_hari'] + $datang['belum_tiba_gt_7_hari'] + $datang['batal'])
        ->toBe($datang['spk_terbit_non_gr']);

    expect($data['efisiensi_v2'])->toMatchArray([
        'avg_dwell_hours_sepatu' => 264,      // H; A diambil November
        'median_dwell_hours_sepatu' => 264,
        'n_sepatu' => 1,
        'avg_dwell_hours_semua' => 156,       // (264 + 48) / 2
    ]);

    expect($data['pengiriman_workshop'])->toMatchArray([
        'spk_dikirim_ke_workshop' => 1,       // log OTW ganda tetap 1 SPK
        'spk_dikirim_ke_workshop_termasuk_ulang' => 1,
        'manifest_gudang_ke_workshop' => 1,
        'manifest_qc_ke_gudang' => 1,
    ]);
});

it('sepatu keluar bulan lalu tidak berubah saat SPK lain di invoice yang sama keluar belakangan', function () {
    momSkenario();

    // November: F keluar 3 Nov, tapi invoice-nya sudah dihitung di Oktober → rupiah November 0.
    $nov = $this->withHeader('X-API-KEY', 'test-secret-key')
        ->getJson('/api/v1/warehouse-summary?start_date=2026-11-01&end_date=2026-11-15&as_of_date=2026-11-15')
        ->assertOk()->json('data.sepatu_keluar_detail');

    expect($nov['pasang_total'])->toBe(2)            // A (5 Nov) & F (3 Nov)
        ->and($nov['invoice_count'])->toBe(0)
        ->and($nov['pasang_lunas_saat_keluar'])->toBe(1)   // F: invoice lunas 2 Nov
        ->and($nov['pasang_belum_lunas_saat_keluar'])->toBe(0)
        ->and($nov['pasang_tanpa_invoice'])->toBe(1);      // A tanpa invoice
});

it('ready_for_pickup_sepatu hanya menghitung SPK SELESAI belum diambil yang sepatunya di rak sepatu', function () {
    momSkenario();

    // R: SELESAI belum diambil, di rak hanya aksesoris → masuk ready_for_pickup, tidak masuk ready_for_pickup_sepatu.
    $r = momWo(['spk_number' => 'UJI-MOM-R', 'status' => 'SELESAI', 'created_at' => '2026-09-05 10:00:00', 'updated_at' => '2026-10-22 10:00:00',
        'finished_date' => '2026-10-22 10:00:00']);
    momLog($r, 'SELESAI', 'STATUS_CHANGE', '2026-10-22 10:00:00');
    momAssignment($r, 'accessories', '2026-10-22 11:00:00', null);

    // S: sudah diambil 25 Okt tapi assignment sepatunya masih aktif (tidak dikeluarkan dari rak)
    //    → masuk shoes_in_rack_count, tidak masuk ready_for_pickup_sepatu.
    $s = momWo(['spk_number' => 'UJI-MOM-S', 'status' => 'SELESAI', 'created_at' => '2026-09-05 10:00:00', 'updated_at' => '2026-10-25 10:00:00',
        'finished_date' => '2026-10-15 10:00:00', 'taken_date' => '2026-10-25 10:00:00']);
    momLog($s, 'SELESAI', 'STATUS_CHANGE', '2026-10-15 10:00:00');
    momAssignment($s, 'shoes', '2026-10-16 10:00:00', null);

    // T: masih PRODUCTION tapi tercatat di rak sepatu → masuk shoes_in_rack_count saja.
    $t = momWo(['spk_number' => 'UJI-MOM-T', 'status' => 'PRODUCTION', 'created_at' => '2026-10-01 10:00:00', 'updated_at' => '2026-10-10 10:00:00']);
    momLog($t, 'PRODUCTION', 'STATUS_CHANGE', '2026-10-10 10:00:00');
    momAssignment($t, 'shoes', '2026-10-11 10:00:00', null);

    $posisi = $this->withHeader('X-API-KEY', 'test-secret-key')
        ->getJson(MOM_URL.'&as_of_date=2026-10-31')->assertOk()->json('data.posisi_as_of');

    expect($posisi['ready_for_pickup'])->toBe(2)              // A, R (field lama, semua kategori)
        ->and($posisi['ready_for_pickup_sepatu'])->toBe(1)     // A
        ->and($posisi['shoes_in_rack_count'])->toBe(3)         // A, S, T
        ->and($posisi['ready_for_pickup_sepatu'])->toBeLessThanOrEqual($posisi['shoes_in_rack_count'])
        ->and($posisi['ready_for_pickup_sepatu'])->toBeLessThanOrEqual($posisi['ready_for_pickup']);
});
