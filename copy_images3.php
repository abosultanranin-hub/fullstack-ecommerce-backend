<?php

// Correct path - note the doubled "statck"
$baseDir = 'C:/xampp/htdocs/full statck/statck/ecommirce1';
chdir($baseDir);

$sourceDir = $baseDir . '/storage/app/public/products';
$destDir = $baseDir . '/public/storage/products';

echo "Current dir: " . getcwd() . "\n";
echo "Source: $sourceDir\n";
echo "Source exists: " . (is_dir($sourceDir) ? "yes" : "no") . "\n";

if (!is_dir($destDir)) {
    echo "Creating directory: $destDir\n";
    if (mkdir($destDir, 0755, true)) {
        echo "Created successfully\n";
    } else {
        echo "Failed to create directory\n";
    }
}

$files = glob($sourceDir . '/*');
echo "Found: " . count($files) . " files\n";

foreach ($files as $file) {
    $filename = basename($file);
    $destFile = $destDir . '/' . $filename;
    echo "Copying $filename to $destFile... ";
    if (copy($file, $destFile)) {
        echo "OK (" . filesize($destFile) . " bytes)\n";
    } else {
        echo "FAILED\n";
    }
}

echo "Done!";
