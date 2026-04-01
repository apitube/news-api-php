<?php

declare(strict_types=1);

namespace APITube;

use APITube\Exceptions\ApiException;
use APITube\Exceptions\AuthenticationException;
use APITube\Exceptions\RateLimitException;
use APITube\Responses\ArticleList;
use APITube\Responses\BalanceResponse;
use APITube\ValueObjects\ApiKey;
use APITube\ValueObjects\BaseUri;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Client\ClientInterface;

/**
 * APITube News API client.
 *
 * Provides methods for accessing news articles, headlines, stories,
 * and account balance through the APITube REST API.
 *
 * @see https://docs.apitube.io/
 */
class Client
{
    private readonly Transporter $transporter;

    /**
     * Create a new APITube client instance.
     *
     * @param string               $apiKey     API key for authentication
     * @param string               $baseUrl    Base URL of the APITube API
     * @param ClientInterface|null $httpClient Optional PSR-18 HTTP client (auto-discovered if null)
     */
    public function __construct(
        string $apiKey,
        string $baseUrl = 'https://api.apitube.io',
        ?ClientInterface $httpClient = null,
    ) {
        $this->transporter = new Transporter(
            apiKey: new ApiKey($apiKey),
            baseUri: new BaseUri($baseUrl),
            client: $httpClient,
        );
    }

    /**
     * Check API availability.
     *
     * @return bool True if the API responds successfully, false otherwise
     */
    public function ping(): bool
    {
        try {
            $this->transporter->getRaw('/ping');

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Retrieve the current account balance and plan information.
     *
     * @return BalanceResponse Account balance details
     *
     * @throws ApiException            On API errors
     * @throws AuthenticationException|JsonException  On invalid API key
     */
    public function balance(): BalanceResponse
    {
        $data = $this->transporter->get('/v1/balance');

        return BalanceResponse::fromArray($data);
    }

    /**
     * Fetch news articles from the specified endpoint.
     *
     * Supported endpoints: 'everything', 'top-headlines', 'story', 'article'.
     * The 'story' endpoint requires an 'id' key in $params.
     *
     * @param string               $endpoint API endpoint name
     * @param array<string, mixed> $params   Query/body parameters for the request
     *
     * @return ArticleList Paginated list of articles
     *
     * @throws InvalidArgumentException                   On unknown endpoint or missing required params
     * @throws ApiException            On API errors
     * @throws AuthenticationException  On invalid API key
     * @throws RateLimitException|JsonException       When rate limit is exceeded
     */
    public function news(string $endpoint, array $params = [], string $version = 'v1'): ArticleList
    {
        $path = match ($endpoint) {
            'everything' => "/{$version}/news/everything",
            'top-headlines' => "/{$version}/news/top-headlines",
            'story' => "/{$version}/news/story/" . ($params['id'] ?? throw new InvalidArgumentException('Story endpoint requires an "id" parameter.')),
            'article' => "/{$version}/news/article",
            default => throw new InvalidArgumentException("Unknown endpoint: {$endpoint}"),
        };

        $body = $params;
        if ($endpoint === 'story') {
            unset($body['id']);
        }

        $data = $this->transporter->post($path, $body);

        return ArticleList::fromArray($data);
    }
}
