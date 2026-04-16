<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Current dir: " . __DIR__ . "\n";
echo "Storage path: " . storage_path() . "\n";
echo "Public path: " . public_path() . "\n";
echo "Base path: " . base_path() . "\n";

$storageProducts = storage_path('app/public/products');
echo "\nStorage products: $storageProducts\n";
echo "Exists: " . (is_dir($storageProducts) ? 'yes' : 'no') . "\n";

if (is_dir($storageProducts)) {
    $files = glob($storageProducts . '/*');
    echo "Files count: " . count($files) . "\n";
    foreach ($files as $file) {
        echo " - " . basename($file) . "\n";
    }
}

$publicStorage = public_path('storage');
echo "\nPublic storage: $publicStorage\n";
echo "Exists: " . (file_exists($publicStorage) ? 'yes' : 'no') . "\n";
echo "Is dir: " . (is_dir($publicStorage) ? 'yes' : 'no') . "\n";
echo "Is link: " . (is_link($publicStorage) ? 'yes' : 'no') . "\n";
