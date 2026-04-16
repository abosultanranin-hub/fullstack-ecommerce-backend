<?php

$baseDir = __DIR__;
$sourceDir = $baseDir . '/storage/app/public/products';
$destDir = $baseDir . '/public/storage/products';

echo "Source: $sourceDir\n";
echo "Source exists: " . (is_dir($sourceDir) ? "yes" : "no") . "\n";

// Create destination directory
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
    echo "Created: $destDir\n";
}

// Copy files
$files = glob($sourceDir . '/*');
echo "Found: " . count($files) . " files\n";

foreach ($files as $file) {
    $filename = basename($file);
    $destFile = $destDir . '/' . $filename;
    echo "Copying $filename... ";
    if (copy($file, $destFile)) {
        echo "OK (" . filesize($destFile) . " bytes)\n";
    } else {
        echo "FAILED\n";
    }
}

echo "\nDone!";
