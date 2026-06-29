<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\Customer;
use App\Enums\WorkOrderStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;

class ArchiveHubTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;
    protected $csUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create cs user
        $this->csUser = User::create([
            'name' => 'CS User',
            'email' => 'cs@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'cs',
        ]);
    }

    /** @test */
    public function guests_cannot_access_archive_hub()
    {
        $response = $this->get(route('admin.archive-hub'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function cs_users_cannot_access_archive_hub()
    {
        // cs user has no admin permission
        $response = $this->actingAs($this->csUser)
            ->get(route('admin.archive-hub'));
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_users_can_access_archive_hub()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.archive-hub'));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function it_lists_work_orders_for_archiving_excluding_spk_pending()
    {
        // Create an SPK_PENDING order
        $pendingOrder = WorkOrder::factory()->create([
            'spk_number' => 'SPK-PENDING-001',
            'status' => WorkOrderStatus::SPK_PENDING,
        ]);

        // Create a SELESAI order
        $selesaiOrder = WorkOrder::factory()->create([
            'spk_number' => 'SPK-SELESAI-001',
            'status' => WorkOrderStatus::SELESAI,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Admin\ArchiveHub::class)
            ->assertStatus(200)
            ->assertSee('SPK-SELESAI-001')
            ->assertDontSee('SPK-PENDING-001');
    }

    /** @test */
    public function it_can_bulk_archive_active_work_orders_to_history()
    {
        $order = WorkOrder::factory()->create([
            'spk_number' => 'SPK-TO-ARCHIVE',
            'status' => WorkOrderStatus::PRODUCTION,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Admin\ArchiveHub::class)
            ->set('selectedIds', [(string)$order->id])
            ->call('archiveSelected')
            ->assertStatus(200);

        $order->refresh();
        $this->assertEquals(WorkOrderStatus::HISTORY, $order->status);
    }

    /** @test */
    public function it_can_bulk_restore_archived_work_orders_from_history()
    {
        $order = WorkOrder::factory()->create([
            'spk_number' => 'SPK-TO-RESTORE',
            'status' => WorkOrderStatus::HISTORY,
            'previous_status' => WorkOrderStatus::PRODUCTION,
        ]);

        Livewire::actingAs($this->adminUser)
            ->test(\App\Livewire\Admin\ArchiveHub::class)
            ->set('activeTab', 'archived')
            ->set('selectedIds', [(string)$order->id])
            ->call('restoreSelected')
            ->assertStatus(200);

        $order->refresh();
        $this->assertEquals(WorkOrderStatus::PRODUCTION, $order->status);
    }
}
