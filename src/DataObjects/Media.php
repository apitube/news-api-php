<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Media attachment associated with an article.
 *
 * Represents an image, video, or other media resource
 * embedded in or related to the article.
 */
class Media
{
    /**
     * @param string|null $url  Media resource URL
     * @param string|null $type Media type (e.g. 'image', 'video', 'audio')
     */
    public function __construct(
        public readonly ?string $url,
        public readonly ?string $type,
    ) {}

    /**
     * Create a Media instance from an API response array.
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
