# APITube News API PHP SDK

[![GitHub Release](https://img.shields.io/github/v/release/apitube/news-api-php)](https://github.com/apitube/news-api-php/releases)
[![Latest Stable Version](https://img.shields.io/packagist/v/apitube/news-api)](https://packagist.org/packages/apitube/news-api)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/apitube/news-api/php)](https://packagist.org/packages/apitube/news-api)
[![License](https://img.shields.io/packagist/l/apitube/news-api)](https://packagist.org/packages/apitube/news-api)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/apitube/news-api-php/pulls)
[![PSR-18 Compatible](https://img.shields.io/badge/PSR--18-compatible-blue.svg)](https://www.php-fig.org/psr/psr-18/)
[![Made with Love](https://img.shields.io/badge/Made%20with-%E2%9D%A4-red.svg)](https://apitube.io)

PHP SDK for the [APITube News API](https://apitube.io) — access global news articles, headlines, stories, sentiment analysis, and more.

- [API Documentation](https://docs.apitube.io/)
- [Website](https://apitube.io)
- [More examples](https://github.com/apitube/news-api-workflows)
- [Cookbook](https://apitube.io/cookbook)

## Requirements

- PHP 8.1+
- A PSR-18 HTTP client (e.g. [Guzzle](https://docs.guzzlephp.org/en/stable/))
- A PSR-17 HTTP factory

## Installation

```bash
composer require apitube/news-api
```

## Quick Start

```php
use APITube\Client;

$client = new Client(apiKey: 'your-api-key');

// Search news articles
$response = $client->news('everything', [
    'title' => 'artificial intelligence',
    'language.code' => 'en',
    'per_page' => 5,
]);

foreach ($response->articles as $article) {
    echo $article->title . "\n";
    echo $article->url . "\n\n";
}
```

## Usage

### Initialize the client

```php
use APITube\Client;

$client = new Client(
    apiKey: 'your-api-key',
    baseUrl: 'https://api.apitube.io', // optional, default value
);
```

You can pass any PSR-18 HTTP client:

```php
$client = new Client(
    apiKey: 'your-api-key',
    httpClient: new \GuzzleHttp\Client(['timeout' => 30]),
);
```

### Search articles

```php
$response = $client->news('everything', [
    'title' => 'climate change',
    'language.code' => 'en',
    'per_page' => 10,
]);

echo "Page: {$response->page}\n";
echo "Has next page: " . ($response->hasNextPages ? 'yes' : 'no') . "\n";

foreach ($response->articles as $article) {
    echo "{$article->title}\n";
    echo "Source: {$article->source?->domain}\n";
    echo "Sentiment: {$article->sentiment?->overall?->polarity}\n";

    // English translation of the headline for non-English articles
    // (null for English articles — fall back to the original title)
    echo "English title: " . ($article->translations?->en?->title ?? $article->title) . "\n\n";
}
```

### Search in plain language

Instead of assembling filters by hand, describe what you want in the `prompt` parameter. The API
translates the sentence into the regular filters before searching and returns what it used in
`meta.prompt`:

```php
$response = $client->news('everything', [
    'prompt' => 'Tesla and Elon Musk news in English for the last 10 days',
    'per_page' => 5,
]);

// ['person.name' => 'Elon Musk', 'organization.name' => 'Tesla', 'language.code' => 'en', 'published_at.start' => 'NOW-10DAY']
print_r($response->meta['prompt']['applied']);
print_r($response->meta['prompt']['ignored']); // values understood but not used, each with a reason
var_dump($response->meta['prompt']['cached']); // true = served from cache, no extra charge
```

The prompt must be 3–500 characters. Filters you pass yourself always win over the prompt.
Translating a prompt costs 2 extra points, but only the first time a given wording is used —
interpretations are cached for 24 hours. See the
[`prompt` reference](https://docs.apitube.io/platform/news-api/parameters#prompt).

### Specify API version

```php
$response = $client->news('everything', [
    'title' => 'artificial intelligence',
    'per_page' => 5,
], version: 'v1');
```

By default, the SDK uses `v1`.

### Top headlines

```php
$response = $client->news('top-headlines', [
    'language.code' => 'en',
    'per_page' => 10,
]);

foreach ($response->articles as $article) {
    echo "{$article->title} — {$article->source?->domain}\n";
}
```

### Get a single article

```php
$response = $client->news('article', [
    'id' => 'article-id',
]);

$article = $response->articles[0];
echo $article->title . "\n";
echo $article->body . "\n";
```

### Get articles by story

```php
$response = $client->news('story', [
    'id' => 'story-id',
]);

foreach ($response->articles as $article) {
    echo "{$article->title}\n";
}
```

### Raw articles

Fetch recently discovered articles before parsing and enrichment:

```php
$response = $client->news('raw', [
    'per_page' => 50,
    'sort.by' => 'published_at',
    'sort.order' => 'desc',
]);

foreach ($response->articles as $article) {
    echo "{$article->title}\n";
}
```

### Count articles

Count articles matching the same filters as `everything`:

```php
$count = $client->count([
    'title' => 'artificial intelligence',
    'language.code' => 'en',
]);

echo "Matching articles: {$count}\n";
```

### Autocomplete suggestions

Supported types: `categories`, `topics`, `industries`, `entities`.

```php
$items = $client->suggest('categories', 'spo');

foreach ($items as $item) {
    echo "{$item['name']} (id: {$item['id']})\n";
}
```

### Reference data (people, companies, sources, journalists)

Each entity exposes a paginated list method and a profile method by ID:

```php
// List
$people = $client->people(['name' => 'Elon', 'per_page' => 5]);
foreach ($people->results as $person) {
    echo "{$person['name']} (id: {$person['id']})\n";
}

// Profile with coverage statistics
$profile = $client->person($people->results[0]['id']);
echo "Articles: {$profile['coverage']['article_count']}\n";

// Same shape for the other entities:
$client->companies(['name' => 'Tesla']);
$client->company($id);
$client->sources(['country' => 1]);
$client->source($id);
$client->journalists(['name' => 'Smith']);
$client->journalist($id);
```

### Check balance

```php
$balance = $client->balance();

echo "Plan: {$balance->plan}\n";
echo "Points: {$balance->points}\n";
```

### Ping

```php
$isAvailable = $client->ping();
echo $isAvailable ? 'API is available' : 'API is unavailable';
```

## Error Handling

The SDK throws typed exceptions:

```php
use APITube\Exceptions\ApiException;
use APITube\Exceptions\AuthenticationException;
use APITube\Exceptions\RateLimitException;

try {
    $response = $client->news('everything', ['title' => 'php']);
} catch (AuthenticationException $e) {
    // Invalid or missing API key (HTTP 401)
    echo "Auth error: {$e->getMessage()}\n";
} catch (RateLimitException $e) {
    // Rate limit exceeded (HTTP 429)
    echo "Rate limited. Retry after: {$e->retryAfter} seconds\n";
} catch (ApiException $e) {
    // Other API errors
    echo "API error ({$e->getCode()}): {$e->getMessage()}\n";
    echo "Request ID: {$e->requestId}\n";
}
```

## Testing

```bash
composer install
vendor/bin/phpunit
```

## License

MIT
