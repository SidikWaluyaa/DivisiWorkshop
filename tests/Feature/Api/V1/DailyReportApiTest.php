<?php

use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

/*
 * Report Harian (GET /api/v1/daily-report). Skenario dihitung manual untuk tanggal 2026-11-19 (Kamis);
 * Senin minggu itu 2026-11-16, bulan berjalan mulai 2026-11-01.
 */

const DR_URL = '/api/v1/daily-report';

beforeEach(function () {
    config(['app.dashboard_api_key' => 'test-secret-key']);
});

function drWo(string $spk, array $attributes = []): int
{
    $row = WorkOrder::factory()->make()->getAttributes();

    return DB::table('work_orders')->insertGetId(array_merge($row, [
        'spk_number' => $spk, 'invoice_id' => null, 'deleted_at' => null,
        'prep_washing_completed_at' => null, 'prep_sol_completed_at' => null, 'prep_upper_completed_at' => null,
        'qc_jahit_completed_at' => null, 'qc_cleanup_completed_at' => null, 'qc_final_completed_at' => null,
    ], $attributes));
}

function drLead(?string $channel): int
{
    return DB::table('cs_leads')->insertGetId([
        'customer_name' => 'Uji', 'customer_phone' => '0800'.random_int(100000, 999999), 'channel' => $channel,
        'status' => 'CLOSING', 'created_at' => '2026-11-01 08:00:00', 'updated_at' => '2026-11-01 08:00:00',
    ]);
}

function drSpk(int $leadId, string $at, ?string $deletedAt = null): void
{
    static $n = 0;
    $customerId = DB::table('customers')->insertGetId([
        'name' => 'Uji', 'phone' => '0811'.random_int(100000, 999999), 'created_at' => $at, 'updated_at' => $at,
    ]);
    DB::table('cs_spk')->insert([
        'cs_lead_id' => $leadId, 'spk_number' => 'UJI-DR-'.(++$n), 'customer_id' => $customerId,
        'services' => '[]', 'total_price' => 0, 'status' => 'HANDED_TO_WORKSHOP',
        'created_at' => $at, 'updated_at' => $at, 'deleted_at' => $deletedAt,
    ]);
}

function drOto(int $woId, string $status, string $createdAt, ?string $respondedAt, string $price, ?string $deletedAt = null): void
{
    DB::table('otos')->insert([
        'work_order_id' => $woId, 'proposed_services' => '[]', 'total_normal_price' => $price, 'total_oto_price' => $price,
        'total_discount' => 'Rp. 0', 'discount_percent' => 0, 'valid_until' => '2026-12-31 23:59:59', 'dp_required' => 'no',
        'status' => $status, 'customer_responded_at' => $respondedAt,
        'created_at' => $createdAt, 'updated_at' => $createdAt, 'deleted_at' => $deletedAt,
    ]);
}

function drPay(string $type, int $amount, string $paidAt, bool $verified = true): void
{
    DB::table('order_payments')->insert([
        'type' => $type, 'amount_total' => $amount, 'payment_method' => 'TRANSFER', 'paid_at' => $paidAt,
        'is_verified' => $verified, 'created_at' => $paidAt, 'updated_at' => $paidAt,
    ]);
}

function drLog(int $woId, string $step, string $action, string $at, string $desc = 'uji'): void
{
    DB::table('work_order_logs')->insert([
        'work_order_id' => $woId, 'step' => $step, 'action' => $action, 'description' => $desc,
        'created_at' => $at, 'updated_at' => $at,
    ]);
}

function drManifest(string $number, string $at, ?string $deletedAt = null): void
{
    DB::table('workshop_manifests')->insert([
        'manifest_number' => $number, 'status' => 'RECEIVED', 'dispatched_at' => $at,
        'created_at' => $at, 'updated_at' => $at, 'deleted_at' => $deletedAt,
    ]);
}

function drService(int $woId, int $cost, string $at, array $details = []): void
{
    DB::table('work_order_services')->insert([
        'work_order_id' => $woId, 'custom_service_name' => 'Uji', 'cost' => $cost,
        'service_details' => json_encode($details), 'created_at' => $at, 'updated_at' => $at,
    ]);
}

function drAfter(int $woId, string $stage, string $at): void
{
    DB::table('cx_after_confirmations')->insert([
        'work_order_id' => $woId, 'round' => 1, 'stage_type' => $stage, 'entered_at' => $at,
        'created_at' => $at, 'updated_at' => $at,
    ]);
}

function drSkenario(): void
{
    // CS: 6 closing pada 19 Nov (1 lagi dihapus), 2 pada 17 Nov, 1 pada Minggu 15 Nov, 1 pada 20 Nov.
    [$online, $offline, $fu, $ig, $tanpa] = [drLead('ONLINE'), drLead('OFFLINE'), drLead('FOLLOW_UP'), drLead('Instagram'), drLead('')]; // channel kosong ada di production (12 lead)
    drSpk($online, '2026-11-19 08:15:00');
    drSpk($online, '2026-11-19 09:30:00');
    drSpk($offline, '2026-11-19 09:45:00');
    drSpk($fu, '2026-11-19 14:00:00');
    drSpk($ig, '2026-11-19 15:00:00');
    drSpk($tanpa, '2026-11-19 16:00:00');
    drSpk($online, '2026-11-19 10:00:00', '2026-11-19 11:00:00');
    drSpk($online, '2026-11-17 10:00:00');
    drSpk($offline, '2026-11-17 11:00:00');
    drSpk($online, '2026-11-15 10:00:00');
    drSpk($online, '2026-11-20 10:00:00');

    $a = drWo('UJI-DR-A', ['prep_washing_completed_at' => '2026-11-19 09:00:00', 'qc_cleanup_completed_at' => '2026-11-19 10:00:00',
        'qc_final_completed_at' => '2026-11-18 16:00:00']);
    $b = drWo('UJI-DR-B', ['prep_washing_completed_at' => '2026-11-19 09:05:00', 'qc_jahit_completed_at' => '2026-11-18 15:00:00']);
    $c = drWo('UJI-DR-C', ['prep_sol_completed_at' => '2026-11-19 11:00:00', 'prep_upper_completed_at' => '2026-11-19 12:00:00']);

    // OTO.
    drOto($a, 'COMPLETED', '2026-11-19 09:00:00', '2026-11-19 10:00:00', 'Rp. 60.000');
    drOto($a, 'CANCELLED', '2026-11-19 09:10:00', null, 'Rp. 90.000', '2026-11-19 12:00:00');
    drOto($b, 'ACCEPTED', '2026-11-05 09:00:00', '2026-11-19 11:00:00', 'Rp. 185.000');
    drOto($b, 'PENDING_CX', '2026-11-05 09:30:00', null, 'Rp. 50.000');
    drOto($c, 'COMPLETED', '2026-11-10 09:00:00', '2026-11-10 10:00:00', 'Rp. 275.000');
    drOto($c, 'COMPLETED', '2026-10-30 09:00:00', '2026-10-31 10:00:00', 'Rp. 99.000');
    drPay('OTO', 60238, '2026-11-19 00:00:00');
    drPay('OTO', 50000, '2026-11-19 00:00:00', false);
    drPay('OTO', 100000, '2026-11-12 00:00:00');
    drPay('TAMBAH_JASA', 75000, '2026-11-19 00:00:00');

    // Sepatu keluar workshop (manifest outbound).
    drManifest('MNF-OUT-20261119-AAAA', '2026-11-19 13:00:00');
    drManifest('MNF-OUT-20261119-BBBB', '2026-11-19 13:30:00', '2026-11-19 14:00:00');
    drManifest('MNF-OUT-20261103-CCCC', '2026-11-03 13:00:00');
    $desc = fn ($m) => "Diterbitkan dalam Manifest Outbound #{$m} oleh uji";
    drLog($a, 'OUTBOUND_QC', 'MANIFEST_GENERATED', '2026-11-19 13:00:00', $desc('MNF-OUT-20261119-AAAA'));
    drLog($b, 'OUTBOUND_QC', 'MANIFEST_GENERATED', '2026-11-19 13:00:00', $desc('MNF-OUT-20261119-AAAA'));
    drLog($b, 'OUTBOUND_QC', 'MANIFEST_GENERATED', '2026-11-19 13:00:01', $desc('MNF-OUT-20261119-AAAA'));
    drLog($c, 'OUTBOUND_QC', 'MANIFEST_GENERATED', '2026-11-19 13:30:00', $desc('MNF-OUT-20261119-BBBB'));
    drLog($a, 'OUTBOUND_QC', 'MANIFEST_GENERATED', '2026-11-03 13:00:00', $desc('MNF-OUT-20261103-CCCC'));

    // Tambah jasa.
    drService($a, 100000, '2026-11-19 10:00:00', ['is_tambah_jasa' => true]);
    drService($a, 50000, '2026-11-19 11:00:00', ['is_cx_additional' => true]);
    drService($b, 75000, '2026-11-19 12:00:00', ['hk_days' => 0]);
    drLog($b, 'PRODUCTION', 'SERVICE_ADDED', '2026-11-19 12:00:01');
    drService($c, 200000, '2026-11-19 12:30:00', ['hk_days' => 0]);
    drLog($c, 'ASSESSMENT', 'SERVICE_ADDED', '2026-11-19 12:30:00');
    drService($c, 300000, '2026-11-19 08:00:00');
    drService($b, 40000, '2026-11-18 10:00:00', ['is_tambah_jasa' => true]);

    // QC After (tahap OTO).
    drAfter($a, 'OTO', '2026-11-19 14:00:00');
    drAfter($b, 'OTO', '2026-11-19 15:00:00');
    drAfter($c, 'REGULER', '2026-11-19 15:30:00');
}

it('menolak date tidak valid atau di masa depan dengan 422', function (string $value) {
    $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(DR_URL.'?date='.$value)
        ->assertStatus(422)->assertJsonStructure(['message']);
})->with(['2026-13-01', '2026-02-30', '19-11-2026', 'kemarin', '2099-01-01']);

it('mewajibkan API key', function () {
    $this->getJson(DR_URL.'?date=2026-11-19')->assertStatus(401);
});

it('tanpa date memakai hari ini', function () {
    $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(DR_URL)
        ->assertOk()->assertJsonPath('data.tanggal', now()->toDateString());
});

it('menghitung semua blok report harian', function () {
    drSkenario();

    $res = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(DR_URL.'?date=2026-11-19')->assertOk();
    $res->assertJsonPath('status', 'success');
    expect($res->getContent())->toContain('"closing_per_jam":{"0":0,"1":0,');
    $d = $res->json('data');

    expect($d['tanggal'])->toBe('2026-11-19')
        ->and(array_keys($d))->toBe(['tanggal', 'dibuat_pada', 'cs', 'oto', 'tambah_jasa', 'qc_after', 'pendukung', 'gudang']);

    $cs = $d['cs'];
    expect(array_sum($cs['closing_per_jam']))->toBe(6)
        ->and($cs['closing_per_jam'])->toMatchArray(['8' => 1, '9' => 2, '10' => 0, '14' => 1, '15' => 1, '16' => 1])
        ->and($cs['per_channel'])->toBe(['ONLINE' => 2, 'OFFLINE' => 1, 'FOLLOW_UP' => 1, 'LAINNYA' => 2])
        ->and(array_sum($cs['per_channel']))->toBe(6)
        ->and($cs['minggu_ini'])->toBe([
            ['tanggal' => '2026-11-16', 'closing' => 0],
            ['tanggal' => '2026-11-17', 'closing' => 2],
            ['tanggal' => '2026-11-18', 'closing' => 0],
            ['tanggal' => '2026-11-19', 'closing' => 6],
        ]);

    expect($d['oto'])->toBe([
        'penawaran' => 2, 'closing_spk' => 2, 'closing_omset' => 245000,
        'bayar_spk' => 1, 'bayar_nominal' => 60238, 'sepatu_keluar_ws' => 2,
        'bulan_ini' => [
            'penawaran' => 5, 'closing_spk' => 3, 'closing_omset' => 520000,
            'bayar_spk' => 2, 'bayar_nominal' => 160238, 'sepatu_keluar_ws' => 3,
        ],
    ]);

    expect($d['tambah_jasa'])->toBe(['spk' => 2, 'jasa' => 3, 'nominal' => 225000]);
    expect($d['qc_after'])->toBe(['after' => 2, 'sudah_qc' => 1]);
    expect($d['pendukung'])->toBe(['cuci' => 2, 'clean_up' => 1, 'jahit_sol' => 0, 'bongkar_sol' => 1, 'bongkar_upper' => 1]);
});

function gdWo(string $spk, string $status, ?string $entryDate = null, array $attributes = []): int
{
    return drWo($spk, array_merge(['status' => $status, 'entry_date' => $entryDate ?? '2020-01-01 00:00:00'], $attributes));
}

function gdSpk(int $woId, int $leadId, string $at): void
{
    static $n = 0;
    $customerId = DB::table('customers')->insertGetId([
        'name' => 'Uji', 'phone' => '0812'.random_int(100000, 999999), 'created_at' => $at, 'updated_at' => $at,
    ]);
    DB::table('cs_spk')->insert([
        'cs_lead_id' => $leadId, 'work_order_id' => $woId, 'spk_number' => 'UJI-GD-'.(++$n), 'customer_id' => $customerId,
        'services' => '[]', 'total_price' => 0, 'status' => 'HANDED_TO_WORKSHOP', 'created_at' => $at, 'updated_at' => $at,
    ]);
}

it('menghitung blok gudang: sepatu masuk, arus harian, dan kontrol status', function () {
    // Sepatu masuk 18 Nov.
    [$offline, $online, $fu] = [drLead('OFFLINE'), drLead('ONLINE'), drLead('FOLLOW_UP')];
    gdSpk(gdWo('UJI-GD-M1', 'PRODUCTION', '2026-11-18 09:00:00'), $offline, '2026-11-16 09:00:00');
    gdSpk(gdWo('UJI-GD-M2', 'PRODUCTION', '2026-11-18 10:00:00'), $online, '2026-11-16 09:00:00');
    gdSpk(gdWo('UJI-GD-M3', 'PRODUCTION', '2026-11-18 19:00:00'), $online, '2026-11-16 09:00:00');       // setelah 18.00
    gdSpk(gdWo('UJI-GD-M4', 'PRODUCTION', '2026-11-18 17:59:59'), $fu, '2026-11-16 09:00:00');
    gdWo('UJI-GD-M5', 'PRODUCTION', '2026-11-18 11:00:00');                                               // tanpa lead
    gdSpk(gdWo('UJI-GD-M6', 'BATAL', '2026-11-18 12:00:00'), $online, '2026-11-16 09:00:00');            // batal
    gdWo('UJI-GD-M7', 'PRODUCTION', '2026-11-18 12:00:00', ['deleted_at' => '2026-11-18 13:00:00']);       // dihapus
    gdWo('UJI-GD-M8', 'PRODUCTION', '2026-11-17 12:00:00');                                               // hari lain

    // Arus gudang (log STATUS_CHANGE).
    $a = gdWo('UJI-GD-A', 'PRODUCTION');
    $b = gdWo('UJI-GD-B', 'PRODUCTION');
    $c = gdWo('UJI-GD-C', 'PRODUCTION');
    drLog($a, 'DITERIMA', 'STATUS_CHANGE', '2026-11-18 08:00:00');
    drLog($a, 'DITERIMA', 'STATUS_CHANGE', '2026-11-18 08:00:02');        // dobel → 1
    drLog($b, 'DITERIMA', 'STATUS_CHANGE', '2026-11-18 09:00:00');
    drLog($b, 'DITERIMA', 'SERVICE_ADDED', '2026-11-18 09:10:00');        // bukan STATUS_CHANGE
    drLog($a, 'READY_TO_DISPATCH', 'STATUS_CHANGE', '2026-11-18 10:00:00');
    drLog($a, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:05:16');
    drLog($a, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:05:19');    // dobel → 1
    drLog($b, 'PREPARATION', 'STATUS_CHANGE', '2026-11-18 11:00:00');
    drLog($c, 'PREPARATION', 'STATUS_CHANGE', '2026-11-18 11:30:00');
    drLog($c, 'DITERIMA', 'STATUS_CHANGE', '2026-11-02 08:00:00');
    drLog($c, 'DITERIMA', 'STATUS_CHANGE', '2026-10-31 08:00:00');        // bulan lalu

    // Kontrol status saat ini.
    foreach (['DITERIMA', 'DITERIMA', 'ASSESSMENT', 'WAITING_PAYMENT', 'READY_TO_DISPATCH', 'CX_FOLLOWUP', 'WAITING_VERIFICATION'] as $i => $st) {
        gdWo('UJI-GD-K'.$i, $st);
    }
    gdWo('UJI-GD-KX', 'DITERIMA', null, ['deleted_at' => '2026-11-18 13:00:00']);

    $g = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(DR_URL.'?date=2026-11-18')->assertOk()->json('data.gudang');

    expect($g['sepatu_masuk'])->toBe([
        'offline' => 1, 'online' => 2, 'follow_up' => 1, 'lainnya' => 1, 'total' => 5,
        'sd_18' => ['offline' => 1, 'online' => 1, 'follow_up' => 1, 'lainnya' => 1, 'total' => 4],
    ]);
    expect([$g['before'], $g['print_spk'], $g['kirim_ws'], $g['diterima_ws']])->toBe([2, 1, 1, 2]);

    $perHari = $g['bulan_ini']['per_hari'];
    expect($perHari)->toHaveCount(18)
        ->and($perHari[0])->toBe(['tanggal' => '2026-11-01', 'before' => 0, 'print_spk' => 0, 'kirim_ws' => 0, 'diterima_ws' => 0, 'otw_ws' => 0])
        ->and($perHari[1])->toBe(['tanggal' => '2026-11-02', 'before' => 1, 'print_spk' => 0, 'kirim_ws' => 0, 'diterima_ws' => 0, 'otw_ws' => 0])
        // otw_ws 0: log OTW di skenario ini tidak menyebut manifest MFST.
        ->and($perHari[17])->toBe(['tanggal' => '2026-11-18', 'before' => 2, 'print_spk' => 1, 'kirim_ws' => 1, 'diterima_ws' => 2, 'otw_ws' => 0]);

    $k = $g['kontrol'];
    expect($k['tertahan'])->toBe(['DITERIMA' => 2, 'ASSESSMENT' => 1, 'WAITING_PAYMENT' => 1, 'total' => 4])
        ->and($k['belum_kirim'])->toBe(['READY_TO_DISPATCH' => 1, 'CX_FOLLOWUP' => 1, 'total' => 6])
        ->and($k['waiting_verification'])->toBe(1)
        ->and($k['diambil_pada'])->toBeString();
});

function gdKlaim(int $woId, string $status, string $at): void
{
    DB::table('warranty_claims')->insert([
        'work_order_id' => $woId, 'customer_name' => 'Uji', 'customer_phone' => '0800', 'spk_number' => 'UJI',
        'problem_description' => 'uji', 'problem_photo' => '[]', 'google_review_photo' => 'uji.jpg',
        'status' => $status, 'created_at' => $at, 'updated_at' => $at,
    ]);
}

it('menghitung key Tahap 3 di blok gudang: jenis SPK, OTW WS dari manifest, keluar, klaim, cancel', function () {
    $reg = ['priority' => 'Reguler'];

    // Sepatu masuk per jenis (18 Nov).
    gdWo('UJI-T3-REG', 'PRODUCTION', '2026-11-18 09:00:00', $reg);
    gdWo('UJI-T3-RND', 'PRODUCTION', '2026-11-18 10:00:00', ['priority' => 'R&D']);
    gdWo('S-2611-01-0001-AB-GR', 'PRODUCTION', '2026-11-18 11:00:00', $reg);
    gdWo('S-2611-01-0002-AB-GR', 'PRODUCTION', '2026-11-18 19:00:00', ['priority' => 'R&D']);   // GR + R&D → rnd, setelah 18.00
    gdWo('S-2611-01-0003-AB-GR', 'BATAL', '2026-11-18 12:00:00', $reg);                        // batal

    // Manifest gudang → workshop.
    drManifest('MFST-20261118-AAA', '2026-11-18 17:05:00');
    drManifest('MFST-20261118-DEL', '2026-11-18 17:30:00', '2026-11-18 18:00:00');
    drManifest('MFST-20261117-BBB', '2026-11-17 16:00:00');
    $via = fn ($m) => "Status berubah dari READY_TO_DISPATCH ke OTW_WORKSHOP. Note: Sent to Workshop Hijau via Manifest #{$m}";
    [$a, $b, $c, $d, $e, $f, $g] = array_map(fn ($i) => gdWo('UJI-T3-W'.$i, 'PREPARATION', null, $reg), range(1, 7));
    drLog($a, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:05:16', $via('MFST-20261118-AAA'));
    drLog($a, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:05:19', $via('MFST-20261118-AAA'));   // dobel
    drLog($b, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:05:16', $via('MFST-20261118-AAA'));
    drLog($c, 'LOGISTICS', 'ADDED_TO_MANIFEST', '2026-11-19 09:00:00', 'SPK ditambahkan ke Manifest #MFST-20261118-AAA oleh Super Admin (admin@workshop.com).');
    drLog($d, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:05:16', $via('MFST-20261118-AAA'));
    drLog($d, 'LOGISTICS', 'REMOVED_FROM_MANIFEST', '2026-11-18 17:20:00', 'SPK dikeluarkan dari Manifest #MFST-20261118-AAA dan dikembalikan ke antrian siap kirim oleh Super Admin.');
    drLog($e, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 17:30:00', $via('MFST-20261118-DEL'));   // manifest dihapus
    drLog($f, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-17 16:00:00', $via('MFST-20261117-BBB'));
    drLog($g, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-11-18 15:00:00');                                // tanpa manifest

    // Sepatu keluar.
    gdWo('UJI-T3-K1', 'SELESAI', null, $reg + ['taken_date' => '2026-11-18 10:00:00']);
    gdWo('UJI-T3-K2', 'SELESAI', null, $reg + ['taken_date' => '2026-11-18 16:00:00']);
    gdWo('UJI-T3-K3', 'SELESAI', null, $reg + ['taken_date' => '2026-11-17 16:00:00']);
    gdWo('UJI-T3-K4', 'SELESAI', null, $reg + ['taken_date' => '2026-11-18 16:00:00', 'deleted_at' => '2026-11-18 17:00:00']);

    // Klaim garansi.
    gdKlaim($a, 'PENDING', '2026-11-18 09:00:00');
    gdKlaim($b, 'REJECTED', '2026-11-18 10:00:00');
    gdKlaim($c, 'APPROVED', '2026-11-17 10:00:00');

    // Cancel.
    $x1 = gdWo('UJI-T3-X1', 'BATAL', null, $reg);
    drLog($x1, 'BATAL', 'ORDER_CANCELLED', '2026-11-18 10:00:00');
    drLog($x1, 'BATAL', 'STATUS_CHANGE', '2026-11-18 18:00:00');                                      // log kedua, tetap 1 SPK
    drLog(gdWo('UJI-T3-X2', 'BATAL', null, $reg), 'BATAL', 'ORDER_CANCELLED', '2026-11-17 10:00:00');
    drLog(gdWo('UJI-T3-X3', 'BATAL', null, $reg + ['deleted_at' => '2026-11-18 12:00:00']), 'BATAL', 'ORDER_CANCELLED', '2026-11-18 10:00:00');
    drLog(gdWo('UJI-T3-X4', 'PRODUCTION', null, $reg), 'BATAL', 'ORDER_CANCELLED', '2026-11-18 11:00:00'); // sudah dipulihkan

    $gd = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(DR_URL.'?date=2026-11-18')->assertOk()->json('data.gudang');

    expect($gd['sepatu_masuk_jenis'])->toBe([
        'rnd' => 2, 'garansi' => 1, 'reguler' => 1, 'total' => 4,
        'sd_18' => ['rnd' => 1, 'garansi' => 1, 'reguler' => 1, 'total' => 3],
    ])->and($gd['sepatu_masuk_jenis']['total'])->toBe($gd['sepatu_masuk']['total']);

    expect($gd['otw_ws'])->toBe(3)                     // a, b, c (d dikeluarkan, e manifest dihapus, g tanpa manifest)
        ->and($gd['kirim_ws'])->toBe(5)                 // log OTW 18 Nov: a, b, d, e, g
        ->and($gd['sepatu_keluar'])->toBe(2)
        ->and($gd['klaim_garansi'])->toBe(2)
        ->and($gd['cancel'])->toBe(1);

    $perHari = collect($gd['bulan_ini']['per_hari'])->keyBy('tanggal');
    expect($perHari['2026-11-17']['otw_ws'])->toBe(1)
        ->and($perHari['2026-11-18']['otw_ws'])->toBe(3);

    // Sebelum log pembatalan tercatat konsisten → null, bukan 0.
    $juni = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(DR_URL.'?date=2026-06-30')->assertOk()->json('data.gudang');
    expect($juni)->toHaveKey('cancel')->and($juni['cancel'])->toBeNull();
});
