<?php

declare(strict_types=1);

namespace APITube\Exceptions;

use Throwable;

/**
 * Base exception for all APITube API errors.
 *
 * Thrown when the API returns a non-2xx HTTP response.
 * Carries the original HTTP status code and an optional
 * request ID for debugging with APITube support.
 */
class ApiException extends \Exception
{
    /**
     * @param string          $message   Error message from the API response
     * @param int             $code      HTTP status code
     * @param string|null     $requestId Unique request identifier for debugging
     * @param Throwable|null $previous  Previous exception in the chain
     */
    public function __construct(
        string                  $message,
        int                     $code = 0,
        public readonly ?string $requestId = null,
        ?Throwable              $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
