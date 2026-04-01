<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Industry classification for a news article.
 *
 * Represents an industry sector associated with the article content.
 */
class Industry
{
    /**
     * @param string|null $id    Unique industry identifier
     * @param string|null $name  Human-readable industry name
     * @param array|null  $links Related resource links
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?array $links,
    ) {}

    /**
     * Create an Industry instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            links: $data['links'] ?? null,
        );
    }
}
