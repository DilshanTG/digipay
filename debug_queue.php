<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "QUEUE_CONNECTION: " . env('QUEUE_CONNECTION') . "\n";
echo "SUPABASE_URL: " . env('SUPABASE_URL') . "\n";
echo "SUPABASE_KEY exists: " . (empty(env('SUPABASE_KEY')) ? 'No' : 'Yes') . "\n";

try {
    $jobCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
    echo "Pending jobs in 'jobs' table: " . $jobCount . "\n";
} catch (\Exception $e) {
    echo "Jobs table does not exist or error: " . $e->getMessage() . "\n";
}
