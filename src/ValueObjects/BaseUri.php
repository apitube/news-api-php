<?php

declare(strict_types=1);

namespace APITube\ValueObjects;

/**
 * Value object representing the API base URI.
 *
 * Normalizes the URI by stripping trailing slashes on construction.
 */
class BaseUri
{
    public readonly string $value;

    /**
     * @param string $value Base URI string (trailing slashes will be removed)
     */
    public function __construct(string $value)
    {
        $this->value = rtrim($value, '/');
    }

    /**
     * Return the base URI as a plain string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
