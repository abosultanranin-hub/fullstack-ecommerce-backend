<?php

$baseDir = __DIR__;
$productsDir = $baseDir . '/storage/app/public/products';

echo "=== Testing Product Images ===\n\n";

// Check if products directory exists
if (!is_dir($productsDir)) {
    echo "ERROR: Products directory not found at: $productsDir\n";
    exit(1);
}

// List all files
$files = glob($productsDir . '/*');
echo "Found " . count($files) . " image files:\n";

foreach ($files as $file) {
    $filename = basename($file);
    $size = filesize($file);
    echo "  ✓ $filename ($size bytes)\n";
}

echo "\n=== Expected URLs ===\n";
echo "Base URL: http://localhost:8000\n";
echo "Image paths in DB should be: storage/products/<filename>\n\n";

echo "Full URLs:\n";
foreach ($files as $file) {
    $filename = basename($file);
    echo "  http://localhost:8000/storage/products/$filename\n";
}

echo "\n=== Testing via PHP file_get_contents ===\n";
$testUrl = 'http://localhost:8000/storage/products/kids_shirt1.jpg';
echo "Testing: $testUrl\n";

$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'ignore_errors' => true
    ]
]);

$result = @file_get_contents($testUrl, false, $context);
if ($result !== false) {
    echo "✓ SUCCESS: Image is accessible!\n";
} else {
    echo "✗ FAILED: Could not access image\n";
    echo "Note: Make sure Laravel server is running (php artisan serve)\n";
}
