<?php
$storagePath = __DIR__ . '/public/storage';

echo "Checking: $storagePath\n";
echo "Exists: " . (file_exists($storagePath) ? "yes" : "no") . "\n";
echo "Is file: " . (is_file($storagePath) ? "yes" : "no") . "\n";
echo "Is dir: " . (is_dir($storagePath) ? "yes" : "no") . "\n";
echo "Is link: " . (is_link($storagePath) ? "yes" : "no") . "\n";
echo "Is readable: " . (is_readable($storagePath) ? "yes" : "no") . "\n";
echo "Is writable: " . (is_writable($storagePath) ? "yes" : "no") . "\n";

if (is_dir($storagePath)) {
    echo "\nContents:\n";
    $items = scandir($storagePath);
    foreach ($items as $item) {
        echo "  $item\n";
    }
}
