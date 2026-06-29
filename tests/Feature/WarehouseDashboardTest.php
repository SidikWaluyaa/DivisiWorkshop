<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use App\Livewire\Warehouse\Dashboard;

class WarehouseDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected $warehouseUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create warehouse user with access to warehouse.storage
        $this->warehouseUser = User::create([
            'name' => 'Warehouse Staff',
            'email' => 'warehouse@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'gudang',
        ]);

        // Create regular user without access
        $this->regularUser = User::create([
            'name' => 'Regular CS',
            'email' => 'regular_cs@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'cs',
        ]);
    }

    /** @test */
    public function guests_cannot_access_warehouse_dashboard()
    {
        $response = $this->get(route('storage.dashboard'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function unauthorized_roles_cannot_access_warehouse_dashboard()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('storage.dashboard'));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_roles_can_access_warehouse_dashboard_and_filter_by_category()
    {
        // Seed services
        $service1 = Service::create([
            'name' => 'Deep Clean',
            'category' => 'Cleaning',
            'price' => 50000,
        ]);
        
        $service2 = Service::create([
            'name' => 'Repaint Boost',
            'category' => 'Repaint',
            'price' => 120000,
        ]);

        // Seed Work Orders
        $woSortir1 = WorkOrder::create([
            'spk_number' => 'SPK-SORTIR-1',
            'customer_name' => 'Customer A',
            'status' => WorkOrderStatus::SORTIR->value,
            'waktu' => Carbon::now(),
        ]);
        $woSortir1->workOrderServices()->create([
            'service_id' => $service1->id,
            'cost' => 50000,
            'status' => 'PENDING',
            'category_name' => 'Cleaning',
        ]);

        $woSortir2 = WorkOrder::create([
            'spk_number' => 'SPK-SORTIR-2',
            'customer_name' => 'Customer B',
            'status' => WorkOrderStatus::SORTIR->value,
            'waktu' => Carbon::now(),
        ]);
        $woSortir2->workOrderServices()->create([
            'service_id' => $service2->id,
            'cost' => 120000,
            'status' => 'PENDING',
            'category_name' => 'Repaint',
        ]);

        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard'));

        $response->assertStatus(200);

        // Test Livewire component sorting filter
        Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->set('sortirCategory', 'Cleaning')
            ->assertViewHas('serviceCategories', function ($cats) {
                return $cats->contains('Cleaning') && $cats->contains('Repaint');
            });
    }

    /** @test */
    public function authorized_roles_can_filter_warehouse_dashboard_by_estimation_date()
    {
        // Seed Work Orders with estimation dates
        $woSortir1 = WorkOrder::create([
            'spk_number' => 'SPK-SORTIR-DATE-1',
            'customer_name' => 'Customer A',
            'status' => WorkOrderStatus::SORTIR->value,
            'waktu' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        $woSortir2 = WorkOrder::create([
            'spk_number' => 'SPK-SORTIR-DATE-2',
            'customer_name' => 'Customer B',
            'status' => WorkOrderStatus::SORTIR->value,
            'waktu' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(10)->toDateString(),
        ]);

        $test = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->set('sortirEstStart', Carbon::now()->addDays(1)->toDateString())
            ->set('sortirEstEnd', Carbon::now()->addDays(3)->toDateString());

        $summary = $test->instance()->sortirSummary;
        $spkNumbers = collect($summary['items'])->pluck('spk_number');
        
        $this->assertTrue($spkNumbers->contains('SPK-SORTIR-DATE-1'));
        $this->assertFalse($spkNumbers->contains('SPK-SORTIR-DATE-2'));
    }

    /** @test */
    public function authorized_roles_can_export_sortir_pdf_with_category_and_estimation_date_filter()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-sortir-pdf', [
                'category' => 'Cleaning',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'est_start' => Carbon::now()->toDateString(),
                'est_end' => Carbon::now()->addDays(5)->toDateString(),
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function authorized_roles_can_export_production_pdf_with_category_and_estimation_date_filter()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-production-pdf', [
                'category' => 'Repaint',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'est_start' => Carbon::now()->toDateString(),
                'est_end' => Carbon::now()->addDays(5)->toDateString(),
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function authorized_roles_can_filter_warehouse_dashboard_by_qc_entered_date()
    {
        // 1. Seed Work Order in QC status
        $woQC1 = WorkOrder::create([
            'spk_number' => 'SPK-QC-DATE-1',
            'customer_name' => 'Customer QC 1',
            'status' => WorkOrderStatus::QC->value,
            'waktu' => Carbon::now(),
        ]);
        
        // Log entry into QC stage (8 days ago)
        \DB::table('work_order_logs')->insert([
            'work_order_id' => $woQC1->id,
            'step' => 'QC',
            'action' => 'STATUS_CHANGE',
            'created_at' => Carbon::now()->subDays(8),
            'updated_at' => Carbon::now()->subDays(8),
        ]);

        $woQC2 = WorkOrder::create([
            'spk_number' => 'SPK-QC-DATE-2',
            'customer_name' => 'Customer QC 2',
            'status' => WorkOrderStatus::QC->value,
            'waktu' => Carbon::now(),
        ]);
        
        // Log entry into QC stage (2 days ago)
        \DB::table('work_order_logs')->insert([
            'work_order_id' => $woQC2->id,
            'step' => 'QC',
            'action' => 'STATUS_CHANGE',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Test filter (Range: 4 days ago to 1 day ago -> only SPK-QC-DATE-2 matches)
        $test = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->assertStatus(200)
            ->set('qcEnteredStart', Carbon::now()->subDays(4)->toDateString())
            ->set('qcEnteredEnd', Carbon::now()->subDays(1)->toDateString());

        $summary = $test->instance()->qcSummary;
        $spkNumbers = collect($summary['items'])->pluck('spk_number');
        
        $this->assertTrue($spkNumbers->contains('SPK-QC-DATE-2'));
        $this->assertFalse($spkNumbers->contains('SPK-QC-DATE-1'));
    }

    /** @test */
    public function authorized_roles_can_export_qc_pdf_with_qc_entered_date_filter()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-qc-pdf', [
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'qc_start' => Carbon::now()->subDays(5)->toDateString(),
                'qc_end' => Carbon::now()->toDateString(),
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function authorized_roles_can_sort_production_dashboard_by_entered_date()
    {
        // 1. Seed Work Order in PRODUCTION status (first entered production 10 days ago)
        $woProdOld = WorkOrder::create([
            'spk_number' => 'SPK-PROD-OLD',
            'customer_name' => 'Old Customer',
            'status' => WorkOrderStatus::PRODUCTION->value,
            'waktu' => Carbon::now(),
        ]);
        
        \DB::table('work_order_logs')->insert([
            'work_order_id' => $woProdOld->id,
            'step' => 'PRODUCTION',
            'action' => 'STATUS_CHANGE',
            'created_at' => Carbon::now()->subDays(10),
            'updated_at' => Carbon::now()->subDays(10),
        ]);

        // 2. Seed another Work Order in PRODUCTION status (entered production 2 days ago)
        $woProdNew = WorkOrder::create([
            'spk_number' => 'SPK-PROD-NEW',
            'customer_name' => 'New Customer',
            'status' => WorkOrderStatus::PRODUCTION->value,
            'waktu' => Carbon::now(),
        ]);
        
        \DB::table('work_order_logs')->insert([
            'work_order_id' => $woProdNew->id,
            'step' => 'PRODUCTION',
            'action' => 'STATUS_CHANGE',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);

        // Test sorting ASC (oldest first -> SPK-PROD-OLD should be first in items list)
        $testAsc = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->set('productionSort', 'asc');

        $itemsAsc = $testAsc->instance()->productionSummary['items'];
        $this->assertEquals('SPK-PROD-OLD', $itemsAsc[0]['spk_number']);

        // Test sorting DESC (newest first -> SPK-PROD-NEW should be first in items list)
        $testDesc = Livewire::actingAs($this->warehouseUser)
            ->test(Dashboard::class)
            ->set('productionSort', 'desc');

        $itemsDesc = $testDesc->instance()->productionSummary['items'];
        $this->assertEquals('SPK-PROD-NEW', $itemsDesc[0]['spk_number']);
    }

    /** @test */
    public function authorized_roles_can_export_production_pdf_with_sort()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-production-pdf', [
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
                'sort' => 'desc',
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
