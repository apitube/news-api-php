<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * News article category classification.
 *
 * Represents a category assigned to an article by the APITube
 * classification engine, including relevance score and taxonomy info.
 */
class Category
{
    /**
     * @param string|null $id       Unique category identifier
     * @param string|null $name     Human-readable category name
     * @param float|null  $score    Relevance score (0.0–1.0)
     * @param string|null $taxonomy Taxonomy system (e.g. IAB, IPTC)
     * @param array|null  $links    Related resource links
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?float $score,
        public readonly ?string $taxonomy,
        public readonly ?array $links,
    ) {}

    /**
     * Create a Category instance from an API response array.
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
            taxonomy: $data['taxonomy'] ?? null,
            links: $data['links'] ?? null,
        );
    }
}
