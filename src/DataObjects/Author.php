<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Article author information.
 *
 * Represents the person or entity who authored a news article.
 */
class Author
{
    /**
     * @param string|null $id   Unique author identifier
     * @param string|null $name Author display name
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
    ) {}

    /**
     * Create an Author instance from an API response array.
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
        );
    }
}
