<?php

use Illuminate\Support\Facades\Mail;

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Attempting to send mail...\n";

try {
    Mail::raw('This is a test email to verify SMTP configuration.', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Email from Debugger');
    });
    
    echo "Mail sent successfully!\n";
} catch (\Throwable $e) {
    echo "CAUGHT EXCEPTION:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
