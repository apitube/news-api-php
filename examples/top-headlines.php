<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$response = $client->news('top-headlines', [
    'language.code' => 'en',
    'per_page' => 10,
]);

echo "Top Headlines (page {$response->page})\n";
echo str_repeat('=', 40) . "\n\n";

foreach ($response->articles as $i => $article) {
    echo ($i + 1) . ". {$article->title}\n";
    echo "   {$article->source?->domain} | {$article->publishedAt}\n\n";
}
