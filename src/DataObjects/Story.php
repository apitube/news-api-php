<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * News story cluster reference.
 *
 * Represents a group of related articles covering the same
 * news event or topic. Articles sharing the same story ID
 * are considered part of the same news story.
 */
class Story
{
    /**
     * @param string|null $id  Unique story cluster identifier
     * @param string|null $uri Story resource URI
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $uri,
    ) {}

    /**
     * Create a Story instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uri: $data['uri'] ?? null,
        );
    }
}
