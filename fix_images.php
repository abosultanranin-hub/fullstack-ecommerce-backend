<?php

$baseDir = 'C:/xampp/htdocs/full statck/ecommirce1';
$sourceDir = $baseDir . '/storage/app/public/products';
$destDir = $baseDir . '/public/storage/products';

echo "Source: $sourceDir\n";
echo "Destination: $destDir\n";

// Create destination directory using recursive mkdir
if (!file_exists($destDir)) {
    if (!mkdir($destDir, 0755, true)) {
        echo "Failed to create directory\n";
        exit(1);
    }
    echo "Created directory: $destDir\n";
}

// Copy files
$files = glob($sourceDir . '/*.jpg');
echo "Found " . count($files) . " files\n";

foreach ($files as $file) {
    $filename = basename($file);
    $destFile = $destDir . '/' . $filename;
    
    if (copy($file, $destFile)) {
        echo "✓ Copied $filename (" . filesize($destFile) . " bytes)\n";
    } else {
        echo "✗ Failed to copy $filename\n";
    }
}

echo "\nDone!";
