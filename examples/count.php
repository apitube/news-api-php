<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$count = $client->count([
    'title' => 'artificial intelligence',
    'language.code' => 'en',
]);

echo "Matching articles: {$count}\n";
