<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\Service;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Warehouse\Dashboard;

class WarehouseDashboardTest extends TestCase
{
    use RefreshDatabase;

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
    public function authorized_roles_can_export_sortir_pdf_with_category_filter()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-sortir-pdf', [
                'category' => 'Cleaning',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function authorized_roles_can_export_production_pdf_with_category_filter()
    {
        $response = $this->actingAs($this->warehouseUser)
            ->get(route('storage.dashboard.export-production-pdf', [
                'category' => 'Repaint',
                'start_date' => Carbon::now()->subDays(5)->toDateString(),
                'end_date' => Carbon::now()->addDays(5)->toDateString(),
            ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
