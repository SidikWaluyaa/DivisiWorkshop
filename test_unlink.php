<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Http\UploadedFile;

try {
    $controller = new CustomerController();
    $customer = \App\Models\Customer::first();

    $tempPath = storage_path('app/temp_test2.jpg');
    $img = imagecreatetruecolor(2000, 2000);
    imagejpeg($img, $tempPath);
    imagedestroy($img);

    $file = new UploadedFile($tempPath, 'temp_test2.jpg', 'image/jpeg', null, true);
    
    $method = new \ReflectionMethod(CustomerController::class, 'processAndSavePhotoChunk');
    $method->setAccessible(true);
    
    echo "Processing...\n";
    $result = $method->invoke($controller, $customer, $file, 'test', 'general');
    echo "Result ID: $result\n";
    
    echo "Unlinking...\n";
    unlink($file->getPathname());
    echo "Unlink OK!\n";

} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString();
} catch (\Error $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
