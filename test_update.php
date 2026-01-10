<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class MockController {
    use \App\Traits\HasStationTracking;
    public function test($order, $type, $action, $techId, $assigneeId, $step) {
        $this->handleStationUpdate($order, $type, $action, $techId, $assigneeId, $step);
    }
    protected function formatStationName($type)
    {
        return ucwords(str_replace('_', ' ', $type));
    }
}

try {
    // Find an order to test. 
    // We need one that has 'prod_sol_started_at' but not completed if testing finish.
    // Or just any order.
    $o = App\Models\WorkOrder::where('status', 'PRODUCTION')->first();
    if (!$o) { 
        $o = App\Models\WorkOrder::find(26); // Fallback
    }
    if (!$o) { die("Order not found"); }
    
    $u = App\Models\User::first();
    
    echo "Testing FINISH action for Order " . $o->id . "...\n";
    
    $mock = new MockController();
    // Simulate 'finish' action
    $mock->test(
        $o,
        'prod_sol',
        'finish',
        $u->id,
        null,
        'PRODUCTION'
    );
    $o->save();
    echo "Trait Finish Verified.\n";

    // Simulate CheckOverallCompletion Logic
    $needsSol = $o->services->contains(fn($s) => stripos($s->category, 'sol') !== false);
    $needsUpper = $o->services->contains(fn($s) => stripos($s->category, 'upper') !== false || stripos($s->category, 'repaint') !== false);
    $needsCleaning = $o->services->contains(fn($s) => stripos($s->category, 'cleaning') !== false || stripos($s->category, 'whitening') !== false);

    $doneSol = !$needsSol || $o->prod_sol_completed_at;
    $doneUpper = !$needsUpper || $o->prod_upper_completed_at;
    $doneCleaning = !$needsCleaning || $o->prod_cleaning_completed_at;
    
    echo "Sol: " . ($needsSol ? 'Needed' : 'Skipped') . " ($doneSol)\n";
    echo "Upper: " . ($needsUpper ? 'Needed' : 'Skipped') . " ($doneUpper)\n";
    echo "Cleaning: " . ($needsCleaning ? 'Needed' : 'Skipped') . " ($doneCleaning)\n";

    if ($doneSol && $doneUpper && $doneCleaning) {
        echo "Moving to QC...\n";
        $ws = new App\Services\WorkflowService();
        $ws->updateStatus($o, App\Enums\WorkOrderStatus::QC, 'Test Script Finish', $u->id);
        echo "Moved to QC Successfully.\n";
    } else {
        echo "Not moving to QC yet.\n";
    }

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
