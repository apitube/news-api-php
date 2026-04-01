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
    echo "Sentiment: {$article->sentiment?->overall?->polarity}\n\n";
}
```

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
