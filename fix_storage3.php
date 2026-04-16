<?php
// حذف الـ symbolic link القديم وإنشاء مجلد عادي

$baseDir = __DIR__;
$publicStorage = $baseDir . '/public/storage';
$productsDir = $publicStorage . '/products';

echo "=== Step 1: Remove old symbolic link ===\n";

// حذف الرابط القديم إذا كان موجوداً
if (is_link($publicStorage)) {
    if (unlink($publicStorage)) {
        echo "✓ Removed symbolic link: $publicStorage\n";
    }
} elseif (is_dir($publicStorage)) {
    // إذا كان مجلد عادي، احذفه
    if (rmdir($publicStorage)) {
        echo "✓ Removed directory: $publicStorage\n";
    }
} else {
    echo "Nothing to remove\n";
}

echo "\n=== Step 2: Create products directory ===\n";

// إنشاء المجلدات
if (!is_dir($productsDir)) {
    if (mkdir($productsDir, 0755, true)) {
        echo "✓ Created: $productsDir\n";
    } else {
        echo "✗ Failed to create: $productsDir\n";
    }
} else {
    echo "✓ Already exists: $productsDir\n";
}

echo "\n=== Step 3: Copy images ===\n";

$sourceDir = $baseDir . '/storage/app/public/products';

if (!is_dir($sourceDir)) {
    echo "✗ Source not found: $sourceDir\n";
    exit(1);
}

$files = glob($sourceDir . '/*');
echo "Found " . count($files) . " files\n";

foreach ($files as $file) {
    if (is_file($file)) {
        $filename = basename($file);
        $destFile = $productsDir . '/' . $filename;
        
        if (copy($file, $destFile)) {
            echo "✓ Copied: $filename\n";
        }
    }
}

echo "\n=== Test ===\n";
$test = $productsDir . '/kids_shirt1.jpg';
if (file_exists($test)) {
    echo "✓ Success!\n";
    echo "URL: http://localhost:8000/storage/products/kids_shirt1.jpg\n";
} else {
    echo "✗ Failed\n";
}
