<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Creating storage symlink/copy ===\n\n";

$storageProducts = storage_path('app/public/products');
$publicStorage = public_path('storage/products');

echo "Source: $storageProducts\n";
echo "Destination: $publicStorage\n\n";

// Create the directory
if (!is_dir(public_path('storage'))) {
    echo "Creating public/storage directory...\n";
    if (mkdir(public_path('storage'), 0755, true)) {
        echo "✓ Created public/storage\n";
    } else {
        echo "✗ Failed to create public/storage\n";
    }
} else {
    echo "public/storage already exists\n";
}

if (!is_dir(public_path('storage/products'))) {
    echo "Creating public/storage/products directory...\n";
    if (mkdir(public_path('storage/products'), 0755, true)) {
        echo "✓ Created public/storage/products\n";
    } else {
        echo "✗ Failed to create public/storage/products\n";
    }
} else {
    echo "public/storage/products already exists\n";
}

// Copy files
if (is_dir($storageProducts)) {
    $files = glob($storageProducts . '/*');
    echo "\nCopying " . count($files) . " files...\n";
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $destFile = public_path('storage/products') . '/' . $filename;
            if (copy($file, $destFile)) {
                echo "✓ Copied: $filename\n";
            } else {
                echo "✗ Failed: $filename\n";
            }
        }
    }
} else {
    echo "Source directory not found!\n";
}

echo "\n=== Done! ===\n";
echo "Images should now be accessible at: http://localhost:8000/storage/products/\n";
