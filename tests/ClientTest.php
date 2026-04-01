<?php

declare(strict_types=1);

namespace APITube\Tests;

use APITube\Client;
use APITube\Exceptions\AuthenticationException;
use APITube\Exceptions\RateLimitException;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client as MockHttpClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private MockHttpClient $mockClient;
    private Client $client;

    protected function setUp(): void
    {
        $this->mockClient = new MockHttpClient();
        $this->client = new Client(
            apiKey: 'test-key',
            baseUrl: 'https://api.apitube.io',
            httpClient: $this->mockClient,
        );
    }

    public function test_ping_returns_true(): void
    {
        $this->mockClient->addResponse(new Response(200, [], 'pong'));

        $this->assertTrue($this->client->ping());
    }

    public function test_ping_returns_false_on_error(): void
    {
        $this->mockClient->addResponse(new Response(500, [], 'Internal Server Error'));

        $this->assertFalse($this->client->ping());
    }

    public function test_balance(): void
    {
        $this->mockClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'api_key' => 'test-key',
            'points' => 1500,
            'plan' => 'pro',
        ])));

        $balance = $this->client->balance();

        $this->assertSame('test-key', $balance->apiKey);
        $this->assertSame(1500, $balance->points);
        $this->assertSame('pro', $balance->plan);
    }

    public function test_news_everything(): void
    {
        $this->mockClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'results' => [
                [
                    'id' => 'art-1',
                    'title' => 'AI advances',
                    'source' => ['domain' => 'example.com'],
                    'sentiment' => [
                        'overall' => ['score' => 0.8, 'polarity' => 'positive'],
                    ],
                ],
            ],
            'page' => 1,
            'has_next_pages' => true,
            'request_id' => 'req-123',
        ])));

        $response = $this->client->news('everything', ['title' => 'AI']);

        $this->assertCount(1, $response->articles);
        $this->assertSame('art-1', $response->articles[0]->id);
        $this->assertSame('AI advances', $response->articles[0]->title);
        $this->assertSame('example.com', $response->articles[0]->source->domain);
        $this->assertSame('positive', $response->articles[0]->sentiment->overall->polarity);
        $this->assertSame(1, $response->page);
        $this->assertTrue($response->hasNextPages);
        $this->assertSame('req-123', $response->requestId);
    }

    public function test_news_top_headlines(): void
    {
        $this->mockClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'results' => [
                ['id' => 'art-2', 'title' => 'Breaking news'],
            ],
            'page' => 1,
            'has_next_pages' => false,
        ])));

        $response = $this->client->news('top-headlines', ['language.code' => 'en']);

        $this->assertCount(1, $response->articles);
        $this->assertSame('Breaking news', $response->articles[0]->title);
        $this->assertFalse($response->hasNextPages);
    }

    public function test_news_story(): void
    {
        $this->mockClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'results' => [],
            'page' => 1,
            'has_next_pages' => false,
        ])));

        $this->client->news('story', ['id' => 'story-abc']);

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertStringContainsString('/v1/news/story/story-abc', (string) $lastRequest->getUri());
        $this->assertSame('POST', $lastRequest->getMethod());

        $body = json_decode((string) $lastRequest->getBody(), true);
        $this->assertArrayNotHasKey('id', $body);
    }

    public function test_news_story_without_id_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Story endpoint requires an "id" parameter.');

        $this->client->news('story', []);
    }

    public function test_news_article(): void
    {
        $this->mockClient->addResponse(new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'results' => [
                ['id' => 'art-3', 'title' => 'Specific article'],
            ],
            'page' => 1,
            'has_next_pages' => false,
        ])));

        $response = $this->client->news('article', ['url' => 'https://example.com/article']);

        $this->assertCount(1, $response->articles);
        $this->assertSame('Specific article', $response->articles[0]->title);
    }

    public function test_news_invalid_endpoint_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown endpoint: invalid');

        $this->client->news('invalid');
    }

    public function test_authentication_exception_on_401(): void
    {
        $this->mockClient->addResponse(new Response(401, [], json_encode([
            'message' => 'Invalid API key',
        ])));

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');

        $this->client->balance();
    }

    public function test_rate_limit_exception_on_429(): void
    {
        $this->mockClient->addResponse(new Response(429, ['Retry-After' => '60'], json_encode([
            'message' => 'Rate limit exceeded',
        ])));

        try {
            $this->client->balance();
            $this->fail('Expected RateLimitException');
        } catch (RateLimitException $e) {
            $this->assertSame('Rate limit exceeded', $e->getMessage());
            $this->assertSame(60, $e->retryAfter);
        }
    }

    public function test_request_has_api_key_header(): void
    {
        $this->mockClient->addResponse(new Response(200, [], json_encode([
            'api_key' => 'test-key',
            'points' => 100,
            'plan' => 'free',
        ])));

        $this->client->balance();

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertSame('test-key', $lastRequest->getHeaderLine('X-API-Key'));
    }

    public function test_post_request_has_json_content_type(): void
    {
        $this->mockClient->addResponse(new Response(200, [], json_encode([
            'results' => [],
            'page' => 1,
            'has_next_pages' => false,
        ])));

        $this->client->news('everything', ['title' => 'test']);

        $lastRequest = $this->mockClient->getLastRequest();
        $this->assertSame('application/json', $lastRequest->getHeaderLine('Content-Type'));
        $this->assertSame('POST', $lastRequest->getMethod());
    }
}
