<?php
$baseDir = __DIR__;
$publicDir = $baseDir . '/public';
$storageDir = $publicDir . '/storage';

echo "=== Debug ===\n";
echo "Base: $baseDir\n";
echo "Public: $publicDir\n";
echo "Is public dir: " . (is_dir($publicDir) ? "yes" : "no") . "\n";

// List what's in public
echo "\nContents of public:\n";
$items = scandir($publicDir);
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $path = $publicDir . '/' . $item;
    $type = is_dir($path) ? "[DIR]" : (is_link($path) ? "[LINK]" : "[FILE]");
    echo "$type $item\n";
}

// Try to remove storage completely
echo "\n=== Removing storage ===\n";
if (is_link($storageDir)) {
    echo "It's a symbolic link\n";
    if (unlink($storageDir)) {
        echo "✓ Unlinked\n";
    }
} elseif (is_dir($storageDir)) {
    echo "It's a directory\n";
    // Try recursive delete
    rrmdir($storageDir);
    echo "✓ Removed directory\n";
}
    echo "It's a file - deleting\n";
    unlink($storageDir);
    echo "✓ Deleted\n";
} else {
    echo "Nothing exists at storage path\n";
}

// Check again
echo "\n=== After removal ===\n";
$items = scandir($publicDir);
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $path = $publicDir . '/' . $item;
    $type = is_dir($path) ? "[DIR]" : (is_link($path) ? "[LINK]" : "[FILE]");
    echo "$type $item\n";
}

// Now create fresh
echo "\n=== Creating directories ===\n";
if (!is_dir($storageDir)) {
    if (mkdir($storageDir, 0755)) {
        echo "✓ Created storage\n";
    } else {
        echo "✗ Failed to create storage\n";
    }
}

$productsDir = $storageDir . '/products';
if (!is_dir($productsDir)) {
    if (mkdir($productsDir, 0755)) {
        echo "✓ Created products\n";
    } else {
        echo "✗ Failed to create products\n";
    }
}

echo "\n=== Copying files ===\n";
$sourceDir = $baseDir . '/storage/app/public/products';
$files = glob($sourceDir . '/*');

foreach ($files as $file) {
    $filename = basename($file);
    $dest = $productsDir . '/' . $filename;
    if (copy($file, $dest)) {
        echo "✓ $filename\n";
    }
}

echo "\n=== Test ===\n";
if (file_exists($productsDir . '/kids_shirt1.jpg')) {
    echo "✓ Done! Images accessible at http://localhost:8000/storage/products/\n";
}
