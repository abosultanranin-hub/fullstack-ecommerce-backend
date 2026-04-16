<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$products = DB::table('products')->select('id', 'name', 'image', 'price')->where('image', '!=', '')->get();

echo "=== API Response Simulation ===\n\n";
$apiUrl = 'http://localhost:8000';

foreach ($products as $p) {
    $fullUrl = $apiUrl . '/' . $p->image;
    echo "Product: {$p->name}\n";
    echo "DB Image: {$p->image}\n";
    echo "Full URL: $fullUrl\n";
    echo "\n";
}
