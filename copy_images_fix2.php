<?php

/**
 * حل بديل: نسخ الصور إلى public/storage/products/
 */

$baseDir = __DIR__;
$sourceDir = $baseDir . '/storage/app/public/products';
$publicDir = $baseDir . '/public';
$storageDir = $publicDir . '/storage';
$productsDir = $storageDir . '/products';

echo "=== Step 1: Creating public directory ===\n";

if (!is_dir($publicDir)) {
    if (mkdir($publicDir, 0755)) {
        echo "✓ Created: $publicDir\n";
    }
} else {
    echo "✓ Already exists: $publicDir\n";
}

echo "\n=== Step 2: Creating storage directory ===\n";

if (!is_dir($storageDir)) {
    if (mkdir($storageDir, 0755)) {
        echo "✓ Created: $storageDir\n";
    }
} else {
    echo "✓ Already exists: $storageDir\n";
}

echo "\n=== Step 3: Creating products directory ===\n";

if (!is_dir($productsDir)) {
    if (mkdir($productsDir, 0755)) {
        echo "✓ Created: $productsDir\n";
    }
} else {
    echo "✓ Already exists: $productsDir\n";
}

echo "\n=== Step 4: Copying images ===\n";

if (!is_dir($sourceDir)) {
    echo "✗ Source not found: $sourceDir\n";
    exit(1);
}

$files = glob($sourceDir . '/*');
echo "Found " . count($files) . " files\n";

$copied = 0;
foreach ($files as $file) {
    if (is_file($file)) {
        $filename = basename($file);
        $destFile = $productsDir . '/' . $filename;
        
        if (copy($file, $destFile)) {
            echo "✓ $filename\n";
            $copied++;
        }
    }
}

echo "\nCopied: $copied files\n";

// Test
echo "\n=== Test ===\n";
$test = $productsDir . '/kids_shirt1.jpg';
if (file_exists($test)) {
    echo "✓ Success! Image accessible at: http://localhost:8000/storage/products/kids_shirt1.jpg\n";
} else {
    echo "✗ Failed\n";
}
