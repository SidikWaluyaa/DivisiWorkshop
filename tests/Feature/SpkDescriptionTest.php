<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpkDescriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $limuUser;
    protected $unauthorizedUser;
    protected $workOrder;

    protected function setUp(): void
    {
        parent::setUp();

        // Create authorized users
        $this->adminUser = User::create([
            'name' => 'Admin Owner',
            'email' => 'admin@workshop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->limuUser = User::create([
            'name' => 'Limu Admin',
            'email' => 'limu@workshop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create unauthorized user
        $this->unauthorizedUser = User::create([
            'name' => 'CS Staff',
            'email' => 'siwa@workshop.com',
            'password' => bcrypt('password'),
            'role' => 'cs',
        ]);

        // Seed a work order
        $this->workOrder = WorkOrder::create([
            'spk_number' => 'S-2606-24-0001-SW',
            'customer_name' => 'Sidik Normal',
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'waktu' => Carbon::now(),
            'priority' => 'NORMAL',
            'estimation_date' => Carbon::now()->addDays(5)->toDateString(),
        ]);
    }

    /** @test */
    public function admin_can_access_and_update_spk_description()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.orders.show', $this->workOrder->id));

        $response->assertStatus(200);
        $response->assertSee('Deskripsi Khusus SPK');
        $response->assertSee('Edit Deskripsi');

        // Update description via POST route
        $updateResponse = $this->actingAs($this->adminUser)
            ->post(route('admin.orders.update-spk-description', $this->workOrder->id), [
                'spk_description' => 'Tolong prioritaskan reparasi sol sepatu ini.',
            ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'spk_description' => 'Tolong prioritaskan reparasi sol sepatu ini.',
        ]);

        $this->assertDatabaseHas('work_orders', [
            'id' => $this->workOrder->id,
            'spk_description' => 'Tolong prioritaskan reparasi sol sepatu ini.',
        ]);

        // Assert audit log was created
        $this->assertDatabaseHas('work_order_logs', [
            'work_order_id' => $this->workOrder->id,
            'user_id' => $this->adminUser->id,
            'action' => 'SPK_DESCRIPTION_UPDATED',
        ]);
    }

    /** @test */
    public function limu_can_access_and_update_spk_description()
    {
        // Update description via POST route as Limu
        $updateResponse = $this->actingAs($this->limuUser)
            ->post(route('admin.orders.update-spk-description', $this->workOrder->id), [
                'spk_description' => 'Catatan dari Limu.',
            ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'spk_description' => 'Catatan dari Limu.',
        ]);

        $this->assertDatabaseHas('work_orders', [
            'id' => $this->workOrder->id,
            'spk_description' => 'Catatan dari Limu.',
        ]);
    }

    /** @test */
    public function unauthorized_users_cannot_update_spk_description()
    {
        $response = $this->actingAs($this->unauthorizedUser)
            ->get(route('admin.orders.show', $this->workOrder->id));

        $response->assertStatus(200);
        $response->assertSee('Terunci (Read-Only)');
        $response->assertDontSee('Edit Deskripsi');

        // Try to update via POST endpoint
        $updateResponse = $this->actingAs($this->unauthorizedUser)
            ->post(route('admin.orders.update-spk-description', $this->workOrder->id), [
                'spk_description' => 'Hacker attempt.',
            ]);

        $updateResponse->assertStatus(403);

        // Verify that the unauthorized user can still READ the description set by admin
        $this->workOrder->update(['spk_description' => 'Deskripsi rahasia dari admin']);
        $readResponse = $this->actingAs($this->unauthorizedUser)
            ->get(route('admin.orders.show', $this->workOrder->id));
        $readResponse->assertStatus(200);
        $readResponse->assertSee('Deskripsi rahasia dari admin');
    }
}
