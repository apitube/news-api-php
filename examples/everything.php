<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$response = $client->news('everything', [
    'title' => 'artificial intelligence',
    'language.code' => 'en',
    'per_page' => 5,
]);

echo "Page: {$response->page}\n";
echo "Has next page: " . ($response->hasNextPages ? 'yes' : 'no') . "\n";
echo "Articles found: " . count($response->articles) . "\n\n";

foreach ($response->articles as $article) {
    echo "--- {$article->title} ---\n";
    echo "URL: {$article->url}\n";
    echo "Source: {$article->source?->domain}\n";
    echo "Published: {$article->publishedAt}\n";
    echo "Sentiment: {$article->sentiment?->overall?->polarity}\n";

    if ($article->categories) {
        $names = array_map(fn($c) => $c->name, $article->categories);
        echo "Categories: " . implode(', ', $names) . "\n";
    }

    echo "\n";
}
