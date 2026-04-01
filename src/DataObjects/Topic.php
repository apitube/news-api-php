<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Topic detected in a news article.
 *
 * Represents a subject or theme identified in the article
 * content, with a relevance score indicating confidence.
 */
class Topic
{
    /**
     * @param string|null $id    Unique topic identifier
     * @param string|null $name  Human-readable topic name
     * @param float|null  $score Relevance score (0.0–1.0)
     * @param array|null  $links Related resource links
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?float $score,
        public readonly ?array $links,
    ) {}

    /**
     * Create a Topic instance from an API response array.
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
            score: isset($data['score']) ? (float) $data['score'] : null,
            links: $data['links'] ?? null,
        );
    }
}
