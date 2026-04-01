<?php

declare(strict_types=1);

namespace APITube\ValueObjects;

/**
 * Value object representing an API authentication key.
 *
 * Validates that the key is non-empty on construction.
 */
class ApiKey
{
    /**
     * @param string $value Raw API key string
     *
     * @throws \InvalidArgumentException If the key is empty or whitespace-only
     */
    public function __construct(
        public readonly string $value,
    ) {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('API key cannot be empty.');
        }
    }

    /**
     * Return the API key as a plain string.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->value;
    }
}
