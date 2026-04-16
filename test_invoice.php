<?php

use App\Models\orders;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoiceMail;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Pick the last order
$order = orders::latest()->first();

if (!$order) {
    echo "No orders found. Please create an order first.\n";
    exit;
}

echo "Order Class: " . get_class($order) . "\n";
echo "Testing items relationship...\n";
try {
    $items = $order->items;
    echo "Items count: " . $items->count() . "\n";
} catch (\Exception $e) {
    echo "Relationship error: " . $e->getMessage() . "\n";
}

$order->load(['user']);

echo "Testing invoice for Order #{$order->number} (User: {$order->user->email})\n";

try {
    echo "Generating PDF...\n";
    $pdf = Pdf::loadView('pdf.invoice', ['order' => $order]);
    $pdfOutput = $pdf->output();
    echo "PDF Generated. Size: " . strlen($pdfOutput) . " bytes.\n";

    echo "Sending Email (Driver: " . config('mail.default') . ")...\n";
    
    Mail::to($order->user)->send(new OrderInvoiceMail($order, $pdfOutput));
    
    echo "Email Sent Successfully!\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
