<?php

/**
 * حل بديل: نسخ الصور إلى public/storage/products/
 * لأن الـ symbolic link لا يعمل على Windows بدون صلاحيات المسؤول
 */

$sourceDir = __DIR__ . '/storage/app/public/products';
$destDir = __DIR__ . '/public/storage/products';

echo "=== Creating directory structure ===\n";

// إنشاء المجلد目标
if (!is_dir($destDir)) {
    if (mkdir($destDir, 0755, true)) {
        echo "✓ Created directory: $destDir\n";
    } else {
        echo "✗ Failed to create directory: $destDir\n";
        exit(1);
    }
} else {
    echo "✓ Directory already exists: $destDir\n";
}

// نسخ الملفات
echo "\n=== Copying images ===\n";

if (!is_dir($sourceDir)) {
    echo "✗ Source directory not found: $sourceDir\n";
    exit(1);
}

$files = glob($sourceDir . '/*');
$copied = 0;
$failed = 0;

foreach ($files as $file) {
    if (is_file($file)) {
        $filename = basename($file);
        $destFile = $destDir . '/' . $filename;
        
        if (copy($file, $destFile)) {
            echo "✓ Copied: $filename\n";
            $copied++;
        } else {
            echo "✗ Failed to copy: $filename\n";
            $failed++;
        }
    }
}

echo "\n=== Summary ===\n";
echo "Total files: " . count($files) . "\n";
echo "Copied: $copied\n";
echo "Failed: $failed\n";

echo "\n=== Testing ===\n";
$testFile = $destDir . '/kids_shirt1.jpg';
if (file_exists($testFile)) {
    echo "✓ Test file exists: kids_shirt1.jpg\n";
    echo "Size: " . filesize($testFile) . " bytes\n";
} else {
    echo "✗ Test file not found\n";
}

echo "\n=== URLs ===\n";
echo "Images should now be accessible at:\n";
foreach ($files as $file) {
    $filename = basename($file);
    echo "http://localhost:8000/storage/products/$filename\n";
}

echo "\nDone!";
