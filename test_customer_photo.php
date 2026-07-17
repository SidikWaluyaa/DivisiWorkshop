<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

try {
    $controller = new CustomerController();
    $customer = \App\Models\Customer::first();
    if (!$customer) {
        echo "No customer found.\n";
        exit;
    }

    // Create a dummy image
    $tempPath = storage_path('app/temp_test.jpg');
    $img = imagecreatetruecolor(2000, 2000);
    imagejpeg($img, $tempPath);
    imagedestroy($img);

    $file = new UploadedFile($tempPath, 'temp_test.jpg', 'image/jpeg', null, true); // true = test mode
    
    // Call the protected method using reflection
    $method = new \ReflectionMethod(CustomerController::class, 'processAndSavePhotoChunk');
    $method->setAccessible(true);
    
    echo "Processing...\n";
    $result = $method->invoke($controller, $customer, $file, 'test', 'general');
    echo "Result ID: $result\n";
    echo "Success!\n";

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString();
} catch (\Error $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
