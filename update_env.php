<?php
$envFile = __DIR__ . '/.env';
$content = file_get_contents($envFile);

$updates = [
    'SUPABASE_URL' => 'https://sbsbsibiamqbrdzfzylm.supabase.co',
    'SUPABASE_KEY' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InNic2JzaWJpYW1xYnJkemZ6eWxtIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjczMzUzMzksImV4cCI6MjA4MjkxMTMzOX0.CWXQW_JbeEs5unaecT8vDFwGxNO1k4RPOmE3_bJcLJI',
    'QUEUE_CONNECTION' => 'sync'
];

foreach ($updates as $key => $value) {
    if (preg_match("/^{$key}=/m", $content)) {
        $content = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $content);
    } else {
        $content .= "\n{$key}=\"{$value}\"";
    }
}

file_put_contents($envFile, $content);
echo "Configuration updated: QUEUE_CONNECTION is now 'sync'\n";
