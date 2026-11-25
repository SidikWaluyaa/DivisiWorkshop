<?php

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

/*
 * Export PDF Divisi R&D (/workshop/rnd/export-pdf).
 */

function rndWo(string $spk, string $status, array $attributes = []): int
{
    $row = WorkOrder::factory()->make()->getAttributes();

    return DB::table('work_orders')->insertGetId(array_merge($row, [
        'spk_number' => $spk, 'status' => $status, 'priority' => 'R&D', 'invoice_id' => null, 'deleted_at' => null,
        'entry_date' => '2026-11-20 09:00:00',
    ], $attributes));
}

beforeEach(function () {
    $this->user = User::factory()->create(['is_active' => true]);
});

it('mengunduh PDF laporan R&D berisi semua kelompok SPK', function () {
    $tech = User::factory()->create(['name' => 'Teknisi Uji']);

    $pra = rndWo('RD-UJI-PRA', 'ASSESSMENT');
    $aktif = rndWo('RD-UJI-AKTIF', 'PRODUCTION', [
        'prod_sol_by' => $tech->id, 'prod_sol_started_at' => '2026-11-21 09:00:00',
    ]);
    rndWo('S-UJI-SELESAI', 'SELESAI', ['finished_date' => '2026-11-22 10:00:00']);
    rndWo('S-UJI-BUKAN-RND', 'PRODUCTION', ['priority' => 'Reguler']);

    DB::table('work_order_services')->insert([
        'work_order_id' => $aktif, 'custom_service_name' => 'Lem Jahit Uji', 'category_name' => 'Reparasi Sol',
        'cost' => 210000, 'service_details' => json_encode(['manual_detail' => ['Benang hitam']]),
        'created_at' => now(), 'updated_at' => now(),
    ]);
    DB::table('work_order_rnd_progress')->insert([
        ['work_order_id' => $aktif, 'user_id' => $tech->id, 'stage_title' => 'Uji Rekat Formula B', 'notes' => 'Rekat kuat',
            'photo_path' => 'rnd/tidak-ada.jpg', 'result_status' => 'SUCCESS', 'created_at' => now(), 'updated_at' => now()],
        ['work_order_id' => $pra, 'user_id' => $tech->id, 'stage_title' => 'Catatan awal', 'notes' => null,
            'photo_path' => null, 'result_status' => 'IN_PROGRESS', 'created_at' => now(), 'updated_at' => now()],
    ]);
    DB::table('work_order_logs')->insert([
        'work_order_id' => $aktif, 'step' => 'PREPARATION', 'action' => 'RND_RESEARCH_STARTED',
        'description' => 'Proyek Riset R&D resmi dimulai', 'created_at' => '2026-11-20 10:00:00', 'updated_at' => '2026-11-20 10:00:00',
    ]);

    $res = $this->actingAs($this->user)->get(route('workshop.rnd.export-pdf'));

    $res->assertOk()->assertHeader('content-type', 'application/pdf');
    expect($res->headers->get('content-disposition'))->toContain('Laporan_RnD_')
        ->and(substr($res->getContent(), 0, 4))->toBe('%PDF');
});

it('view PDF menampilkan rincian dan mengikuti filter pencarian', function () {
    rndWo('RD-UJI-CARI', 'PRODUCTION', ['shoe_brand' => 'MerkUnik']);
    rndWo('RD-UJI-LAIN', 'PRODUCTION', ['shoe_brand' => 'Lainnya']);

    $captured = null;
    \Illuminate\Support\Facades\View::composer('workshop.rnd.export_pdf', function ($view) use (&$captured) {
        $captured = ['html' => null, 'data' => $view->getData()];
    });

    $this->actingAs($this->user)->get(route('workshop.rnd.export-pdf', ['search' => 'MerkUnik']))->assertOk();

    $html = view('workshop.rnd.export_pdf', $captured['data'])->render();
    expect($html)->toContain('RD-UJI-CARI')->not->toContain('RD-UJI-LAIN')
        ->and($html)->toContain('Rincian per SPK')
        ->and($captured['data']['summary']['Riset Aktif'])->toBe(1);
});

it('foto progres diperkecil menjadi thumbnail di PDF', function () {
    $wo = rndWo('RD-UJI-FOTO', 'PRODUCTION');
    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('rnd_uji');
    $img = imagecreatetruecolor(900, 1600);
    imagejpeg($img, storage_path('app/public/rnd_uji/foto.jpg'));
    DB::table('work_order_rnd_progress')->insert([
        'work_order_id' => $wo, 'stage_title' => 'Uji foto', 'photo_path' => 'rnd_uji/foto.jpg',
        'result_status' => 'SUCCESS', 'created_at' => now(), 'updated_at' => now(),
    ]);

    $captured = null;
    \Illuminate\Support\Facades\View::composer('workshop.rnd.export_pdf', function ($view) use (&$captured) {
        $captured = $view->getData();
    });

    try {
        $this->actingAs($this->user)->get(route('workshop.rnd.export-pdf'))->assertOk();
    } finally {
        \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('rnd_uji');
    }

    $foto = $captured['groups']['active']['orders']->firstWhere('spk_number', 'RD-UJI-FOTO')->rndProgresses->first()->pdf_photo;
    expect($foto)->toStartWith('data:image/jpeg;base64,');
    [$w, $h] = getimagesizefromstring(base64_decode(substr($foto, strlen('data:image/jpeg;base64,'))));
    expect([$w, $h])->toBe([240, 427]);
});

it('mewajibkan login', function () {
    $this->get(route('workshop.rnd.export-pdf'))->assertRedirect();
});
