<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Hyperlink associated with an article or entity.
 *
 * Represents a typed URL reference (e.g. canonical, Wikipedia, source).
 */
class Link
{
    /**
     * @param string|null $url  Link URL
     * @param string|null $type Link type identifier (e.g. 'canonical', 'wikipedia')
     */
    public function __construct(
        public readonly ?string $url,
        public readonly ?string $type,
    ) {}

    /**
     * Create a Link instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            type: $data['type'] ?? null,
        );
    }
}
