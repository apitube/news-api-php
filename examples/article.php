<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$articleId = $argv[1] ?? '12345';

$response = $client->news('article', [
    'id' => $articleId,
]);

foreach ($response->articles as $article) {
    echo "Title: {$article->title}\n";
    echo "Description: {$article->description}\n";
    echo "URL: {$article->url}\n";
    echo "Language: {$article->language}\n";
    echo "Published: {$article->publishedAt}\n\n";

    if ($article->source) {
        echo "Source: {$article->source->domain}\n";
        echo "Source type: {$article->source->type}\n";
        echo "Source bias: {$article->source->bias}\n\n";
    }

    if ($article->sentiment) {
        echo "Sentiment:\n";
        echo "  Overall: {$article->sentiment->overall?->polarity} ({$article->sentiment->overall?->score})\n";
        echo "  Title: {$article->sentiment->title?->polarity} ({$article->sentiment->title?->score})\n";
        echo "  Body: {$article->sentiment->body?->polarity} ({$article->sentiment->body?->score})\n\n";
    }

    if ($article->entities) {
        echo "Entities:\n";
        foreach ($article->entities as $entity) {
            echo "  - {$entity->name} ({$entity->type}, freq: {$entity->frequency})\n";
        }
    }
}
