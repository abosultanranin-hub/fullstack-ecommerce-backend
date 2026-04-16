<?php

/**
 * Script to download product images from Unsplash
 * Run: php download_products_images.php
 */

// Create products directory if not exists
$storagePath = __DIR__ . '/storage/app/public/products';
if (!file_exists($storagePath)) {
    mkdir($storagePath, 0755, true);
    echo "Created directory: $storagePath\n";
}

// Product images from Unsplash (free to use)
$products = [
    [
        'id' => 1,
        'name' => 'kids_shirt1.jpg',
        'url' => 'https://images.unsplash.com/photo-1596870230751-ebdfce98ec42?w=400'
    ],
    [
        'id' => 2,
        'name' => 'kids_shirt2.jpg',
        'url' => 'https://images.unsplash.com/photo-1503919545889-aef636e10ad4?w=400'
    ],
    [
        'id' => 8,
        'name' => 'men_shirt1.jpg',
        'url' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=400'
    ],
    [
        'id' => 9,
        'name' => 'men_shirt2.jpg',
        'url' => 'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=400'
    ],
    [
        'id' => 15,
        'name' => 'women_dress1.jpg',
        'url' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400'
    ],
    [
        'id' => 16,
        'name' => 'women_dress2.jpg',
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

echo "Starting download of product images...\n";

foreach ($products as $product) {
    $filepath = $storagePath . '/' . $product['name'];
    echo "Downloading: {$product['name']}... ";
    
    if (downloadImage($product['url'], $filepath)) {
        echo "✓ Success\n";
    } else {
        echo "✗ Failed\n";
    }
}

echo "\nDownload complete!\n";
echo "Images saved to: $storagePath\n";

// List downloaded files
echo "\nDownloaded files:\n";
$files = glob($storagePath . '/*');
foreach ($files as $file) {
    echo " - " . basename($file) . " (" . filesize($file) . " bytes)\n";
}
