<?php
/**
 * Simple runner for invoice test
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

include 'test_invoice_system.php';
