<?php

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/*
 * Kamus MoM Workshop (Workshop Tahap 1): blok workshop_mom di /workshop-sync.
 * Skenario dihitung manual per as_of = 2026-10-31 23:59:59, periode 1–31 Okt 2026.
 */

const WS_URL = '/api/v1/workshop-sync?start_date=2026-10-01&end_date=2026-10-31';

beforeEach(function () {
    config(['app.dashboard_api_key' => 'test-secret-key']);
    Cache::flush();
    $this->user = User::factory()->create();
});

function wsWo(array $attributes): int
{
    $row = WorkOrder::factory()->make()->getAttributes();

    return DB::table('work_orders')->insertGetId(array_merge($row, [
        'invoice_id' => null,
        'finished_date' => null,
        'taken_date' => null,
        'warehouse_qc_at' => null,
        'estimation_date' => null,
        'new_estimation_date' => null,
        'fast_track_status' => 'no',
        'priority' => 'Regular',
        'deleted_at' => null,
    ], $attributes));
}

function wsLog(int $woId, string $step, string $action, string $at): void
{
    DB::table('work_order_logs')->insert([
        'work_order_id' => $woId, 'step' => $step, 'action' => $action,
        'description' => 'uji', 'created_at' => $at, 'updated_at' => $at,
    ]);
}

function wsSkenario(): void
{
    // W1: tiba 2 Okt, dikirim ke workshop 3 Okt, selesai 10 Okt (7 hari workshop, 8 hari Kamus), estimasi 12 Okt → tepat waktu.
    $w1 = wsWo(['spk_number' => 'UJI-WS-1', 'status' => 'SELESAI', 'fast_track_status' => 'yes', 'created_at' => '2026-10-01 10:00:00',
        'updated_at' => '2026-10-10 10:00:00', 'entry_date' => '2026-10-02 10:00:00', 'finished_date' => '2026-10-10 10:00:00',
        'estimation_date' => '2026-10-12']);
    wsLog($w1, 'DITERIMA', 'STATUS_CHANGE', '2026-10-02 10:00:00');
    wsLog($w1, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-10-03 10:00:00');
    wsLog($w1, 'SELESAI', 'STATUS_CHANGE', '2026-10-10 10:00:00');

    // W2: tiba 21 Sep (carryover), dikirim 22 Sep, selesai 25 Okt (33 hari workshop, 34 hari Kamus), estimasi 20 Okt → terlambat.
    $w2 = wsWo(['spk_number' => 'UJI-WS-2', 'status' => 'SELESAI', 'created_at' => '2026-09-20 10:00:00',
        'updated_at' => '2026-10-25 10:00:00', 'entry_date' => '2026-09-21 10:00:00', 'finished_date' => '2026-10-25 10:00:00',
        'estimation_date' => '2026-10-20']);
    wsLog($w2, 'DITERIMA', 'STATUS_CHANGE', '2026-09-21 10:00:00');
    wsLog($w2, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-09-22 10:00:00');
    wsLog($w2, 'SELESAI', 'STATUS_CHANGE', '2026-10-25 10:00:00');

    // W3: SPK garansi selesai 5 Okt tanpa log OTW, tanpa estimasi, tanpa tanggal tiba (2 jam Kamus).
    wsWo(['spk_number' => 'UJI-WS-3-GR', 'status' => 'SELESAI', 'created_at' => '2026-10-05 10:00:00', 'updated_at' => '2026-10-05 12:00:00',
        'entry_date' => '2026-10-05 10:00:00', 'finished_date' => '2026-10-05 12:00:00']);

    // W4: dibatalkan 7 Okt (log BATAL/ORDER_CANCELLED, jalur pembatalan yang paling sering), updated_at 8 Okt.
    $w4 = wsWo(['spk_number' => 'UJI-WS-4', 'status' => 'BATAL', 'created_at' => '2026-10-06 10:00:00', 'updated_at' => '2026-10-08 10:00:00']);
    wsLog($w4, 'BATAL', 'ORDER_CANCELLED', '2026-10-07 10:00:00');

    // W5: WIP PRODUCTION per 31 Okt, dikirim 16 Okt (umur 15,58 hari), estimasi 25 Okt → overdue.
    $w5 = wsWo(['spk_number' => 'UJI-WS-5', 'status' => 'PRODUCTION', 'fast_track_status' => 'yes', 'created_at' => '2026-10-15 10:00:00',
        'updated_at' => '2026-10-18 10:00:00', 'estimation_date' => '2026-10-25']);
    wsLog($w5, 'DITERIMA', 'STATUS_CHANGE', '2026-10-15 12:00:00');
    wsLog($w5, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-10-16 10:00:00');
    wsLog($w5, 'PRODUCTION', 'STATUS_CHANGE', '2026-10-18 10:00:00');

    // W6: ASSESSMENT (lewat QC_PASSED) → hanya masuk total versi Kamus.
    $w6 = wsWo(['spk_number' => 'UJI-WS-6', 'status' => 'ASSESSMENT', 'created_at' => '2026-10-19 10:00:00', 'updated_at' => '2026-10-20 10:00:00']);
    wsLog($w6, 'RECEPTION', 'QC_PASSED', '2026-10-20 10:00:00');

    // W7: sekarang SELESAI (5 Nov), tapi per 31 Okt masih QC; dikirim 26 Okt (umur 5,58 hari), tanpa estimasi.
    $w7 = wsWo(['spk_number' => 'UJI-WS-7', 'status' => 'SELESAI', 'created_at' => '2026-10-25 10:00:00', 'updated_at' => '2026-11-05 10:00:00',
        'finished_date' => '2026-11-05 10:00:00']);
    wsLog($w7, 'OTW_WORKSHOP', 'STATUS_CHANGE', '2026-10-26 10:00:00');
    wsLog($w7, 'QC', 'STATUS_CHANGE', '2026-10-28 10:00:00');
    wsLog($w7, 'SELESAI', 'STATUS_CHANGE', '2026-11-05 10:00:00');
}

it('tanpa as_of_date workshop-sync tidak menambah blok workshop_mom', function () {
    $data = $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(WS_URL)->assertOk()->json('data');

    expect($data)->not->toHaveKey('workshop_mom')
        ->and(array_keys($data))->toBe(['matrix', 'metrics', 'analytics', 'operational', 'summary']);
});

it('workshop-sync menolak as_of_date tidak valid dengan 422', function (string $value) {
    $this->withHeader('X-API-KEY', 'test-secret-key')->getJson(WS_URL.'&as_of_date='.$value)->assertStatus(422);
})->with(['2026-13-01', '2026-02-30', '31-10-2026', '2099-01-01']);

it('menghitung workshop_mom periode dan posisi per as_of_date', function () {
    wsSkenario();

    $mom = $this->withHeader('X-API-KEY', 'test-secret-key')
        ->getJson(WS_URL.'&as_of_date=2026-10-31')->assertOk()->json('data.workshop_mom');

    expect($mom['as_of'])->toBe('2026-10-31 23:59:59');

    $p = $mom['periode'];
    expect($p['output_selesai'])->toBe(3)              // W1, W2, W3
        ->and($p['output_garansi'])->toBe(1)
        ->and($p['hari_kerja'])->toBe(27);              // 31 hari − 4 Minggu

    expect($p['lead_time_workshop'])->toMatchArray([
        'n' => 2, 'tanpa_log_otw' => 1, 'avg_hari' => 20, 'median_hari' => 20, 'p80_hari' => 27.8, 'p90_hari' => 30.4,
        'le_14_hari' => 1, 'd15_30_hari' => 0, 'gt_30_hari' => 1,
    ]);
    expect($p['lead_time_kamus'])->toMatchArray([
        'n' => 3, 'avg_hari' => 14, 'median_hari' => 8, 'le_14_hari' => 2, 'd15_30_hari' => 0, 'gt_30_hari' => 1,
    ]);
    foreach (['lead_time_workshop', 'lead_time_kamus'] as $k) {
        expect($p[$k]['le_14_hari'] + $p[$k]['d15_30_hari'] + $p[$k]['gt_30_hari'])->toBe($p[$k]['n']);
    }

    expect($p['otc'])->toMatchArray(['n_dengan_estimasi' => 2, 'tepat_waktu' => 1, 'terlambat' => 1, 'tanpa_estimasi' => 1,
        'metode_estimasi' => 'nilai_sekarang']);
    expect($p['batal'])->toMatchArray(['spk_batal' => 1, 'spk_dibuat_non_gr' => 5, 'spk_batal_waktu_dari_log' => 1]);
    expect($p['fast_track'])->toMatchArray(['spk_dibuat' => 5, 'yes' => 2, 'no' => 3, 'lainnya' => 0]);
    expect($p['carryover'])->toMatchArray(['output_dari_tiba_bulan_ini' => 1, 'output_dari_tiba_sebelumnya' => 1, 'output_tanpa_tanggal_tiba' => 1]);
    expect(array_sum($p['carryover']))->toBe($p['output_selesai']);

    $pos = $mom['posisi'];
    expect($pos['wip_bengkel'])->toMatchArray(['SORTIR' => 0, 'PREPARATION' => 0, 'PRODUCTION' => 1, 'QC' => 1, 'REVISI' => 0, 'total' => 2])
        ->and($pos['wip_bengkel']['total'])->toBe($pos['wip_bengkel']['SORTIR'] + $pos['wip_bengkel']['PREPARATION']
            + $pos['wip_bengkel']['PRODUCTION'] + $pos['wip_bengkel']['QC'] + $pos['wip_bengkel']['REVISI'])
        ->and($pos['wip_kamus_total'])->toBe(3);       // + W6 ASSESSMENT
    expect($pos['overdue'])->toMatchArray(['wip_bengkel_overdue' => 1, 'wip_bengkel_tanpa_estimasi' => 1]);
    expect($pos['umur_wip_bengkel'])->toMatchArray(['median_hari' => 10.58, 'gt_14_hari' => 1, 'gt_30_hari' => 0, 'n' => 2, 'tanpa_log_otw' => 0]);
});
