<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MaterialManagementService;
use App\Models\Material;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestMaterialWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-material-workflow 
                            {material_id? : ID Material to test} 
                            {qty? : Quantity needed}
                            {--wo= : Work Order ID (Optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete material workflow (Check Stock -> Reserve/Po -> Result)';

    /**
     * Execute the console command.
     */
    public function handle(MaterialManagementService $service)
    {
        // Login as Admin for testing context
        $user = User::where('role', 'admin')->first() ?? User::first();
        if ($user) {
            Auth::login($user);
            $this->info("Logged in as: {$user->name}");
        } else {
            $this->error("No user found to login.");
            return;
        }

        $materialId = $this->argument('material_id');
        $qty = $this->argument('qty');
        $woId = $this->option('wo');

        // Interactive Mode if no arguments
        if (!$materialId) {
            $materialId = $this->ask('Enter Material ID');
        }

        if (!$qty) {
            $qty = $this->ask('Enter Quantity', 1);
        }
        
        $material = Material::find($materialId);
        if (!$material) {
            $this->error("Material ID {$materialId} not found.");
            return;
        }

        $this->info("Testing Material: {$material->name} ({$material->category})");
        $this->info("Current Stock: {$material->stock}");
        $this->info("Reserved Stock: {$material->reserved_stock}");
        $this->info("Available: {$material->getAvailableStock()}");
        $this->newLine();

        // Prepare Payload
        $materials = [
            [
                'material_id' => $materialId,
                'quantity' => (float) $qty,
                'notes' => 'Test via Console'
            ]
        ];

        $this->warn("Running processCompleteMaterialWorkflow...");
        
        try {
            $result = $service->processCompleteMaterialWorkflow($materials, $woId, null, "Console Test");

            $this->table(
                ['Key', 'Value'],
                [
                    ['Success', $result['success'] ? 'YES' : 'NO'],
                    ['Message', $result['message']],
                    ['Shopping Request', $result['shopping_request'] ? $result['shopping_request']->request_number : '-'],
                    ['Reservations', count($result['production_reservations'])],
                    ['Unavailable', count($result['unavailable_materials'])],
                ]
            );

            // Detail Unavailable
            if (!empty($result['unavailable_materials'])) {
                $this->error("Unavailable Materials (Need PO):");
                foreach ($result['unavailable_materials'] as $item) {
                    $this->line("- {$item['material']->name}: Shortage {$item['shortage']}");
                }
            }

            // Detail Shopping
            if ($result['shopping_request']) {
                $this->info("Shopping Request Created: " . $result['shopping_request']->request_number);
            }

             // Detail Reservations
            if (!empty($result['production_reservations'])) {
                $this->info("Reservations Created:");
                foreach ($result['production_reservations'] as $res) {
                    $this->line("- ID: {$res->id}, Qty: {$res->quantity}");
                }
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
