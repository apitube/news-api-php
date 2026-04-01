<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$storyId = $argv[1] ?? 'example-story-id';

$response = $client->news('story', [
    'id' => $storyId,
]);

echo "Story: {$storyId}\n";
echo "Articles in story: " . count($response->articles) . "\n\n";

foreach ($response->articles as $article) {
    echo "- {$article->title}\n";
    echo "  {$article->url}\n\n";
}
