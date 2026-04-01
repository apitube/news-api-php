<?php

declare(strict_types=1);

namespace APITube\Exceptions;

/**
 * Thrown when API authentication fails (HTTP 401).
 *
 * Typically indicates an invalid, expired, or missing API key.
 */
class AuthenticationException extends ApiException
{
}
