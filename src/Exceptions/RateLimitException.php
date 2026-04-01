<?php

declare(strict_types=1);

namespace APITube\Exceptions;

/**
 * Thrown when the API rate limit is exceeded (HTTP 429).
 *
 * Contains an optional Retry-After value indicating how many
 * seconds to wait before making a new request.
 */
class RateLimitException extends ApiException
{
    /**
     * @param string          $message    Error message
     * @param int             $code       HTTP status code (default 429)
     * @param int|null        $retryAfter Seconds to wait before retrying
     * @param string|null     $requestId  Unique request identifier for debugging
     * @param \Throwable|null $previous   Previous exception in the chain
     */
    public function __construct(
        string $message = 'Rate limit exceeded',
        int $code = 429,
        public readonly ?int $retryAfter = null,
        ?string $requestId = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $requestId, $previous);
    }
}
