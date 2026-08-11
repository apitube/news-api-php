<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

// Describe the news in plain language — the API turns the sentence into the regular
// filters before searching and reports what it used in meta.prompt.
$response = $client->news('everything', [
    'prompt' => 'Tesla and Elon Musk news in English for the last 10 days',
    'per_page' => 5,
]);

$prompt = $response->meta['prompt'] ?? null;

if ($prompt) {
    echo "Interpreted as:\n";

    foreach ($prompt['applied'] ?? [] as $key => $value) {
        echo "  {$key} = {$value}\n";
    }

    foreach ($prompt['ignored'] ?? [] as $item) {
        echo "  ignored {$item['field']}=\"{$item['value']}\" ({$item['reason']})\n";
    }

    // Repeating the same wording within 24 hours is served from cache and costs nothing extra.
    echo 'Cached: ' . (($prompt['cached'] ?? false) ? 'yes' : 'no') . "\n\n";
}

echo 'Articles found: ' . count($response->articles) . "\n\n";

foreach ($response->articles as $article) {
    echo "--- {$article->title} ---\n";
    echo "URL: {$article->url}\n";
    echo "Source: {$article->source?->domain}\n";
    echo "Published: {$article->publishedAt}\n\n";
}
