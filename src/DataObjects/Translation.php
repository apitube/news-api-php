<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Machine translation of an article into a single target language.
 *
 * Only the headline and the description are translated; the article
 * body is always returned in its original language.
 */
class Translation
{
    /**
     * @param string|null $title       Article headline in the target language
     * @param string|null $description Article description in the target language
     */
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $description,
    ) {}

    /**
     * Create a Translation instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}
