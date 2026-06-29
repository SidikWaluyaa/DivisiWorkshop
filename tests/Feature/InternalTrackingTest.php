<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\WorkOrder;
use App\Enums\WorkOrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use App\Livewire\InternalTracking;

class InternalTrackingTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@sistemworkshop.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function guests_cannot_access_internal_tracking()
    {
        $response = $this->get(route('internal-tracking.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authorized_users_can_access_internal_tracking()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('internal-tracking.index'));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function internal_tracking_displays_correct_priority_badges()
    {
        // Seed Work Orders with different priorities
        $woPrioritas = WorkOrder::create([
            'spk_number' => 'S-2606-20-0010-SW',
            'customer_name' => 'WWSS Prioritas',
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'waktu' => Carbon::now(),
            'priority' => 'PRIORITAS',
            'estimation_date' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        $woReguler = WorkOrder::create([
            'spk_number' => 'S-2606-02-0002-SW',
            'customer_name' => 'Sidik Reguler',
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'waktu' => Carbon::now(),
            'priority' => 'REGULER',
            'estimation_date' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        $woNormal = WorkOrder::create([
            'spk_number' => 'S-2606-02-0001-SW',
            'customer_name' => 'Sidik Normal',
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'waktu' => Carbon::now(),
            'priority' => 'NORMAL',
            'estimation_date' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        // Search for Prioritas
        Livewire::actingAs($this->adminUser)
            ->test(InternalTracking::class)
            ->set('searchKeyword', 'WWSS Prioritas')
            ->assertSee('Prioritas')
            ->assertSee('bg-red-50 text-red-700 border-red-200');

        // Search for Reguler
        Livewire::actingAs($this->adminUser)
            ->test(InternalTracking::class)
            ->set('searchKeyword', 'Sidik Reguler')
            ->assertSee('Reguler')
            ->assertSee('bg-blue-50 text-blue-700 border-blue-200');

        // Search for Normal
        Livewire::actingAs($this->adminUser)
            ->test(InternalTracking::class)
            ->set('searchKeyword', 'Sidik Normal')
            ->assertSee('Normal')
            ->assertSee('bg-slate-50 text-slate-600 border-slate-250');
    }

    /** @test */
    public function internal_tracking_displays_correct_sla_badges()
    {
        // Seed Work Orders with SLA Statuses
        $woSesuai = WorkOrder::create([
            'spk_number' => 'S-2606-10-0001-SW',
            'customer_name' => 'Sesuai Estimasi User',
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'waktu' => Carbon::now(),
            'entry_date' => Carbon::now(),
            'estimation_date' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        $woTerlambat = WorkOrder::create([
            'spk_number' => 'S-2606-10-0002-SW',
            'customer_name' => 'Terlambat User',
            'status' => WorkOrderStatus::ASSESSMENT->value,
            'waktu' => Carbon::now(),
            'entry_date' => Carbon::now()->subDays(5),
            'estimation_date' => Carbon::now()->subDays(1)->toDateString(),
        ]);

        // Search for Sesuai Estimasi
        Livewire::actingAs($this->adminUser)
            ->test(InternalTracking::class)
            ->set('searchKeyword', 'Sesuai Estimasi User')
            ->assertSee('Sesuai Estimasi')
            ->assertSee('bg-emerald-50 text-emerald-700 border-emerald-200');

        // Search for Terlambat
        Livewire::actingAs($this->adminUser)
            ->test(InternalTracking::class)
            ->set('searchKeyword', 'Terlambat User')
            ->assertSee('Terlambat')
            ->assertSee('bg-rose-50 text-rose-700 border-rose-250');
    }
}
