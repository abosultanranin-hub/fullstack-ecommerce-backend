<?php

// Change to the project directory
chdir('C:/xampp/htdocs/full statck/ecommirce1');

$sourceDir = 'storage/app/public/products';
$destDir = 'public/storage/products';

echo "Current dir: " . getcwd() . "\n";
echo "Source exists: " . (is_dir($sourceDir) ? "yes" : "no") . "\n";

if (!is_dir($destDir)) {
    if (mkdir($destDir, 0755, true)) {
        echo "Created: $destDir\n";
    } else {
        echo "Failed to create directory\n";
    }
}

$files = glob($sourceDir . '/*');
echo "Found: " . count($files) . " files\n";

foreach ($files as $file) {
    $filename = basename($file);
    $destFile = $destDir . '/' . $filename;
    echo "Copying $filename... ";
    if (copy($file, $destFile)) {
        echo "OK\n";
    } else {
        echo "FAILED\n";
    }
}

echo "Done!";
