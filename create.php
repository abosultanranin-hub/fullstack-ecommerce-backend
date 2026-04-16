<?php

// إنشاء symbolic link يدوياً

$linkPath = __DIR__ . '/public/storage';
$targetPath = __DIR__ . '/storage/app/public';

// إذا كان المجلد موجوداً، احذفه
if (is_link($linkPath) || is_dir($linkPath)) {
    if (is_link($linkPath)) {
        unlink($linkPath);
        echo "Removed existing link: $linkPath\n";
    } else {
        rmdir($linkPath);
        echo "Removed existing directory: $linkPath\n";
    }
}

// إنشاء الـ symbolic link
if (symlink($targetPath, $linkPath)) {
    echo "✓ Symbolic link created successfully!\n";
    echo "Link: $linkPath\n";
    echo "Target: $targetPath\n";
    
    // التحقق
    if (is_link($linkPath)) {
        echo "✓ Link verified!\n";
    }
} else {
    echo "✗ Failed to create symbolic link\n";
    echo "Trying alternative method...\n";
    
    // طريقة بديلة: نسخ المجلدات
    if (!is_dir($linkPath)) {
        mkdir($linkPath, 0755, true);
    }
    
    $sourceProducts = $targetPath . '/products';
    $destProducts = $linkPath . '/products';
    
    if (is_dir($sourceProducts) && !is_dir($destProducts)) {
        if (symlink($sourceProducts, $destProducts)) {
            echo "✓ Created products symlink: $destProducts -> $sourceProducts\n";
        }
    }
}

echo "\n=== Testing access ===\n";
$testFile = $linkPath . '/products/kids_shirt1.jpg';
if (file_exists($testFile)) {
    echo "✓ File accessible at: $testFile\n";
} else {
    echo "✗ File not found at: $testFile\n";
}

echo "\nDone!";
