<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Geographic location of a news source.
 *
 * Represents the country where the news source is based.
 */
class SourceLocation
{
    /**
     * @param string|null $countryName Full country name (e.g. 'United States')
     * @param string|null $countryCode ISO 3166-1 alpha-2 country code (e.g. 'US')
     */
    public function __construct(
        public readonly ?string $countryName,
        public readonly ?string $countryCode,
    ) {}

    /**
     * Create a SourceLocation instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryName: $data['country_name'] ?? null,
            countryCode: $data['country_code'] ?? null,
        );
    }
}
