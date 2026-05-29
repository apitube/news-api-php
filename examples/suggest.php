<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$items = $client->suggest('categories', 'spo');

echo "Suggestions: " . count($items) . "\n\n";

foreach ($items as $item) {
    echo "- {$item['name']} (id: {$item['id']})\n";
}
