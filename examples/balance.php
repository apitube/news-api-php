<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$balance = $client->balance();

echo "API Key: {$balance->apiKey}\n";
echo "Plan: {$balance->plan}\n";
echo "Points: {$balance->points}\n";
