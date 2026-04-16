<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Testing SMTP Mail Configuration...\n";
echo "================================\n\n";

// Check configuration
echo "Mail Driver: " . config('mail.default') . "\n";
echo "SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
echo "SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
echo "SMTP Username: " . config('mail.mailers.smtp.username') . "\n";
echo "From Address: " . config('mail.from.address') . "\n\n";

// Test sending email
try {
    echo "Attempting to send test email...\n";
    
    Mail::raw('This is a test email from Laravel', function($message) {
        $message->to('test@example.com')
                ->subject('Test Email - ' . now());
    });
    
    echo "✅ Test email sent successfully!\n";
    echo "Check your Mailtrap inbox for the test email.\n";
    
} catch (Exception $e) {
    echo "❌ Error sending email: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
