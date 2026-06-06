<?php

require __DIR__ . '/vendor/autoload.php';

// Load env
if (file_exists(__DIR__ . '/.env')) {
    Dotenv\Dotenv::createImmutable(__DIR__)->load();
} else {
    echo "Warning: .env file not found. Make sure to create one with the necessary environment variables.\n";
    exit(1);
}

$apiKey = $_ENV['SUPABASE_SERVICE_ROLE_KEY'];
$restUrl = $_ENV['SUPABASE_URL'];

// Convert REST URL → SQL RPC URL
$sqlUrl = str_replace('/rest/v1', '', $restUrl) . '/rest/v1/rpc/run_sql';

$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');

foreach ($files as $file) {
    $sql = file_get_contents($file);
    $name = basename($file);

    echo "Running migration: $name\n";

    $response = file_get_contents($sqlUrl, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                "apikey: $apiKey",
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json",
            ],
            'content' => json_encode(['sql' => $sql]),
            'ignore_errors' => true
        ]
    ]));

    echo "Response: $response\n\n";
}

echo "All migrations complete.\n";
