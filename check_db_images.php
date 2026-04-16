
<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$products = DB::table('products')->select('id', 'name', 'image')->get();

echo "=== Products in Database ===\n";
foreach ($products as $p) {
    echo "ID: $p->id | Name: $p->name | Image: $p->image\n";
}
