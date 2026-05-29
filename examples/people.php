<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new APITube\Client(
    apiKey: getenv('APITUBE_API_KEY') ?: 'your-api-key',
    baseUrl: getenv('APITUBE_BASE_URL') ?: 'https://api.apitube.io',
);

$response = $client->people([
    'name' => 'Elon',
    'per_page' => 5,
]);

echo "Page: {$response->page}\n";
echo "Has next page: " . ($response->hasNextPages ? 'yes' : 'no') . "\n";
echo "People found: " . count($response->results) . "\n\n";

foreach ($response->results as $person) {
    echo "--- {$person['name']} ---\n";
    echo "ID: {$person['id']}\n";
    echo "Wikidata: " . ($person['wikidata_id'] ?? '-') . "\n\n";
}

if ($response->results !== []) {
    $id = $response->results[0]['id'];
    $profile = $client->person($id);

    echo "Profile of {$profile['name']}\n";
    echo "Articles: " . ($profile['coverage']['article_count'] ?? 'n/a') . "\n";
}
