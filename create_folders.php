<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Creating directories...\n";

// Get the public path
$publicPath = public_path('storage/products');
echo "Public path: $publicPath\n";

// Create directory
if (!is_dir($publicPath)) {
    if (mkdir($publicPath, 0755, true)) {
        echo "Created: $publicPath\n";
    } else {
        echo "Failed to create: $publicPath\n";
    }
} else {
    echo "Already exists: $publicPath\n";
}

// Copy files
$sourcePath = storage_path('app/public/products');
echo "Source path: $sourcePath\n";

if (is_dir($sourcePath)) {
    $files = glob($sourcePath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            $filename = basename($file);
            $destFile = $publicPath . '/' . $filename;
            if (copy($file, $destFile)) {
                echo "Copied: $filename\n";
            } else {
                echo "Failed to copy: $filename\n";
            }
        }
    }
} else {
    echo "Source directory not found!\n";
}

echo "Done!\n";
