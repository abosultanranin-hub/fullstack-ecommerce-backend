<?php
$testUrl = 'http://localhost:8000/storage/products/kids_shirt1.jpg';

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'ignore_errors' => true
    ]
]);

$result = @file_get_contents($testUrl, false, $context);

if ($result !== false) {
    echo "✓ SUCCESS: Image is accessible!\n";
    echo "Size: " . strlen($result) . " bytes\n";
    echo "URL: $testUrl\n";
} else {
    echo "✗ FAILED: Could not access image\n";
    echo "HTTP Code: " . $http_response_header[0] . "\n";
    echo "URL: $testUrl\n";
}

// Also test is_dir to confirm
echo "\n--- Directory Check ---\n";
$dir = 'C:/xampp/htdocs/full statck/ecommirce1/public/storage/products';
echo "is_dir: " . (is_dir($dir) ? 'yes' : 'no') . "\n";
echo "is_link: " . (is_link($dir) ? 'yes' : 'no') . "\n";
