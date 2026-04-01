<?php

declare(strict_types=1);

namespace APITube;

use APITube\Exceptions\ApiException;
use APITube\Exceptions\AuthenticationException;
use APITube\Exceptions\RateLimitException;
use APITube\ValueObjects\ApiKey;
use APITube\ValueObjects\BaseUri;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * HTTP transport layer for the APITube API.
 *
 * Handles request construction, authentication headers, response parsing,
 * and error-to-exception mapping using PSR-18/PSR-17 interfaces.
 */
class Transporter
{
    private readonly ClientInterface $client;
    private readonly RequestFactoryInterface $requestFactory;
    private readonly StreamFactoryInterface $streamFactory;

    /**
     * @param ApiKey               $apiKey  API key value object for authentication
     * @param BaseUri              $baseUri Base URI value object for the API
     * @param ClientInterface|null $client  Optional PSR-18 HTTP client (auto-discovered if null)
     */
    public function __construct(
        private readonly ApiKey   $apiKey,
        private readonly BaseUri  $baseUri,
        ?ClientInterface $client = null,
    ) {
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * Send a GET request and return the decoded JSON response.
     *
     * @param string $path API endpoint path (e.g. '/v1/balance')
     *
     * @return array<string, mixed> Decoded JSON response body
     *
     * @throws ApiException On non-2xx responses
     * @throws JsonException|ClientExceptionInterface On invalid JSON in response
     */
    public function get(string $path): array
    {
        $request = $this->requestFactory
            ->createRequest('GET', $this->baseUri->toString() . $path)
            ->withHeader('X-API-Key', $this->apiKey->toString())
            ->withHeader('Accept', 'application/json');

        $response = $this->client->sendRequest($request);

        return $this->handleResponse($response);
    }

    /**
     * Send a POST request with a JSON body and return the decoded response.
     *
     * @param string               $path API endpoint path
     * @param array<string, mixed> $body Request body to be JSON-encoded
     *
     * @return array<string, mixed> Decoded JSON response body
     *
     * @throws ApiException On non-2xx responses
     * @throws JsonException|ClientExceptionInterface On invalid JSON in response
     */
    public function post(string $path, array $body = []): array
    {
        $request = $this->requestFactory
            ->createRequest('POST', $this->baseUri->toString() . $path)
            ->withHeader('X-API-Key', $this->apiKey->toString())
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR)));

        $response = $this->client->sendRequest($request);

        return $this->handleResponse($response);
    }

    /**
     * Send a GET request and return the raw response body as a string.
     *
     * @param string $path API endpoint path
     *
     * @return string Raw response body
     *
     * @throws ApiException|ClientExceptionInterface On non-2xx responses
     */
    public function getRaw(string $path): string
    {
        $request = $this->requestFactory
            ->createRequest('GET', $this->baseUri->toString() . $path)
            ->withHeader('X-API-Key', $this->apiKey->toString());

        $response = $this->client->sendRequest($request);
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 200 && $statusCode < 300) {
            return (string) $response->getBody();
        }

        $this->throwForStatus($response);
    }

    /**
     * Decode a JSON response or throw on non-2xx status codes.
     *
     * @param ResponseInterface $response PSR-7 response to handle
     *
     * @return array<string, mixed> Decoded JSON response body
     *
     * @throws ApiException On non-2xx responses
     * @throws JsonException On invalid JSON in response
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($statusCode >= 200 && $statusCode < 300) {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        }

        $this->throwForStatus($response);
    }

    /**
     * Map an HTTP error response to the appropriate exception and throw it.
     *
     * @param ResponseInterface $response The failed PSR-7 response
     *
     * @return never
     *
     * @throws AuthenticationException On 401 responses
     * @throws RateLimitException      On 429 responses (includes Retry-After if present)
     * @throws ApiException            On all other non-2xx responses
     */
    private function throwForStatus(ResponseInterface $response): never
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = json_decode($body, true) ?? [];
        $message = $data['message'] ?? $data['error'] ?? "API error: HTTP {$statusCode}";
        $requestId = $data['request_id'] ?? null;

        throw match (true) {
            $statusCode === 401 => new AuthenticationException($message, $statusCode, $requestId),
            $statusCode === 429 => new RateLimitException(
                message: $message,
                code: $statusCode,
                retryAfter: $response->hasHeader('Retry-After')
                    ? (int) $response->getHeaderLine('Retry-After')
                    : null,
                requestId: $requestId,
            ),
            default => new ApiException($message, $statusCode, $requestId),
        };
    }
}
