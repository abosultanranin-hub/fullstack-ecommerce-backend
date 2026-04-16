<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\SecurityVerificationMail;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$logFile = 'debug_trace.log';
file_put_contents($logFile, "Starting Debug...\n");

function logTrace($e, $file) {
    $output = "EXCEPTION: " . $e->getMessage() . "\n";
    $output .= "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    $output .= "Trace:\n" . $e->getTraceAsString() . "\n";
    file_put_contents($file, $output, FILE_APPEND);
    echo "Exception logged to $file\n";
}

echo "1. Testing Mail::raw...\n";
try {
    Mail::raw('Test Raw', function ($message) {
        $message->to('test_raw@example.com')->subject('Raw Test');
    });
    echo "Mail::raw SUCCESS\n";
    file_put_contents($logFile, "Mail::raw SUCCESS\n", FILE_APPEND);
} catch (\Throwable $e) {
    echo "Mail::raw FAILED\n";
    logTrace($e, $logFile);
}

echo "\n2. Testing SecurityVerificationMail Mailable...\n";
try {
    $token = "123456";
    $ip = "127.0.0.1";
    $country = "PS";
    
    // Simulate what the controller does
    $mailable = new SecurityVerificationMail($token, $ip, $country);
    
    // Attempt to render it first (catch blade errors)
    try {
        $html = $mailable->render();
        echo "Mailable Render SUCCESS\n";
        file_put_contents($logFile, "Mailable Render SUCCESS\n", FILE_APPEND);
    } catch (\Throwable $e) {
        echo "Mailable Render FAILED\n";
        logTrace($e, $logFile);
    }

    // Now try to send
    Mail::to('test_mailable@example.com')->send($mailable);
    
    echo "SecurityVerificationMail Send SUCCESS\n";
    file_put_contents($logFile, "SecurityVerificationMail Send SUCCESS\n", FILE_APPEND);

} catch (\Throwable $e) {
    echo "SecurityVerificationMail Send FAILED\n";
    logTrace($e, $logFile);
}
