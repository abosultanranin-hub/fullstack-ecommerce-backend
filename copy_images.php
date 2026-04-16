<?php

$baseDir = 'C:/xampp/htdocs/full statck/ecommirce1';
$sourceDir = $baseDir . '/storage/app/public/products';
$destDir = $baseDir . '/public/storage/products';

echo "Source: $sourceDir\n";
echo "Source exists: " . (file_exists($sourceDir) ? "yes" : "no") . "\n";

if (!file_exists($destDir)) {
    mkdir($destDir, 0755, true);
    echo "Created: $destDir\n";
}

$files = glob($sourceDir . '/*');
echo "Found: " . count($files) . " files\n";

foreach ($files as $file) {
    $filename = basename($file);
    $destFile = $destDir . '/' . $filename;
    if (copy($file, $destFile)) {
        echo "OK: $filename\n";
    } else {
        echo "FAIL: $filename\n";
    }
}

echo "Done!";
