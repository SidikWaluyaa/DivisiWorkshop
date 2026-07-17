<?php
require 'vendor/autoload.php';
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

try {
    $manager = new ImageManager(new Driver());
    $img = $manager->create(2000, 2000);
    $img2 = $img->scaleDown(width: 1920);
    echo "Original width: " . $img->width() . "\n";
    echo "New width: " . $img2->width() . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
