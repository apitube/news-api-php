<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Geographic location mentioned in an article.
 *
 * Represents a place referenced in the article text,
 * with optional coordinates and classification type.
 */
class Location
{
    /**
     * @param string|null $name    Location display name
     * @param string|null $country Country name or ISO code
     * @param float|null  $lat     Latitude coordinate
     * @param float|null  $lng     Longitude coordinate
     * @param string|null $type    Location type (e.g. 'city', 'country', 'region')
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $country,
        public readonly ?float $lat,
        public readonly ?float $lng,
        public readonly ?string $type,
    ) {}

    /**
     * Create a Location instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            country: $data['country'] ?? null,
            lat: isset($data['lat']) ? (float) $data['lat'] : null,
            lng: isset($data['lng']) ? (float) $data['lng'] : null,
            type: $data['type'] ?? null,
        );
    }
}
