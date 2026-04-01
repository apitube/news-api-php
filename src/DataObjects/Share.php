<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Social media share counts for an article.
 *
 * Tracks the number of times an article has been shared
 * across major social platforms.
 */
class Share
{
    /**
     * @param int|null $total    Total shares across all platforms
     * @param int|null $facebook Facebook share count
     * @param int|null $twitter  Twitter/X share count
     * @param int|null $reddit   Reddit share count
     */
    public function __construct(
        public readonly ?int $total,
        public readonly ?int $facebook,
        public readonly ?int $twitter,
        public readonly ?int $reddit,
    ) {}

    /**
     * Create a Share instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            total: isset($data['total']) ? (int) $data['total'] : null,
            facebook: isset($data['facebook']) ? (int) $data['facebook'] : null,
            twitter: isset($data['twitter']) ? (int) $data['twitter'] : null,
            reddit: isset($data['reddit']) ? (int) $data['reddit'] : null,
        );
    }
}
