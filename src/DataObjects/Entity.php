<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Named entity extracted from an article.
 *
 * Represents a person, organization, location, or other entity
 * detected via NER (Named Entity Recognition), including its
 * frequency and position data within the article.
 */
class Entity
{
    /**
     * @param string|null $id             Unique entity identifier
     * @param string|null $name           Entity surface form as it appears in text
     * @param string|null $type           Entity type (e.g. 'person', 'organization', 'location')
     * @param int|null    $frequency      Number of occurrences in the article
     * @param array|null  $titlePositions Character positions of the entity in the title
     * @param array|null  $bodyPositions  Character positions of the entity in the body
     * @param array|null  $links          Related resource links (e.g. Wikipedia, Wikidata)
     * @param array|null  $metadata       Additional entity metadata
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?string $type,
        public readonly ?int $frequency,
        public readonly ?array $titlePositions,
        public readonly ?array $bodyPositions,
        public readonly ?array $links,
        public readonly ?array $metadata,
    ) {}

    /**
     * Create an Entity instance from an API response array.
     *
     * Position data is extracted from nested 'title.pos' and 'body.pos' keys.
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
            type: $data['type'] ?? null,
            frequency: isset($data['frequency']) ? (int) $data['frequency'] : null,
            titlePositions: $data['title']['pos'] ?? null,
            bodyPositions: $data['body']['pos'] ?? null,
            links: $data['links'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }
}
