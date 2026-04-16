<?php

// Use relative path from the virtual workspace
$baseDir = __DIR__; // This is ecommirce1 folder
$storageDir = $baseDir . '/storage/app/public';

echo "Base dir: $baseDir\n";

// Create directory if not exists
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
    echo "Created: $storageDir\n";
}

// Product images from Unsplash (free to use)
$products = [
    [
        'name' => 'products/kids_shirt1.jpg',
        'url' => 'https://images.unsplash.com/photo-1596870230751-ebdfce98ec42?w=400'
    ],
    [
        'name' => 'products/kids_shirt2.jpg',
        'url' => 'https://images.unsplash.com/photo-1519241987812-04383a180c4e?w=400'
    ],
    [
        'name' => 'products/men_shirt1.jpg',
        'url' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=400'
    ],
    [
        'name' => 'products/men_shirt2.jpg',
        'url' => 'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=400'
    ],
    [
        'name' => 'products/women_dress1.jpg',
        'url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400'
    ],
    [
        'name' => 'products/women_dress2.jpg',
        'url' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=400'
    ]
];

function downloadImage($url, $filepath) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData) {
        if (file_put_contents($filepath, $imageData)) {
            return true;
        }
    }
    return false;
}

echo "Downloading product images...\n\n";

foreach ($products as $product) {
    $fullPath = $storageDir . '/' . $product['name'];
    $dir = dirname($fullPath);
    
    // Create subdirectory if needed
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $filename = basename($product['name']);
    echo "Downloading: $filename... ";
    
    if (downloadImage($product['url'], $fullPath)) {
        $size = filesize($fullPath);
        echo "OK ($size bytes)\n";
    } else {
        echo "FAILED\n";
    }
}

echo "\nDone!\n";

// Verify
$productsDir = $storageDir . '/products';
if (is_dir($productsDir)) {
    $files = glob($productsDir . '/*');
    echo "\nDownloaded " . count($files) . " files to $productsDir\n";
}
