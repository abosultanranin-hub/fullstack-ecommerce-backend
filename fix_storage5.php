<?php
$baseDir = __DIR__;
$publicDir = $baseDir . '/public';
$storageDir = $publicDir . '/storage';

echo "=== Debug ===\n";
echo "Base: $baseDir\n";

// List what's in public
echo "\nContents of public:\n";
$items = scandir($publicDir);
foreach ($items as $item) {
    if ($item === '.' || $item === '..') continue;
    $path = $publicDir . '/' . $item;
    $type = is_dir($path) ? "[DIR]" : (is_link($path) ? "[LINK]" : "[FILE]");
    echo "$type $item\n";
}

// Function to remove directory
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        rmdir($dir);
    }
}

// Try to remove storage
echo "\n=== Removing storage ===\n";
if (is_link($storageDir)) {
    echo "It's a symbolic link\n";
    unlink($storageDir);
    echo "✓ Unlinked\n";
} elseif (is_dir($storageDir)) {
    echo "It's a directory\n";
    rrmdir($storageDir);
    echo "✓ Removed\n";
} else {
    echo "Nothing to remove\n";
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

// Create fresh
echo "\n=== Creating directories ===\n";
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755);
    echo "Created storage\n";
}

$productsDir = $storageDir . '/products';
if (!is_dir($productsDir)) {
    mkdir($productsDir, 0755);
    echo "Created products\n";
}

// Copy files
echo "\n=== Copying files ===\n";
$sourceDir = $baseDir . '/storage/app/public/products';
$files = glob($sourceDir . '/*');

foreach ($files as $file) {
    $filename = basename($file);
    $dest = $productsDir . '/' . $filename;
    copy($file, $dest);
    echo "Copied: $filename\n";
}

echo "\n=== Test ===\n";
if (file_exists($productsDir . '/kids_shirt1.jpg')) {
    echo "SUCCESS! Images at http://localhost:8000/storage/products/\n";
}
