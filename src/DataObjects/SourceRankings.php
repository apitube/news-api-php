<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Source authority and ranking metrics.
 *
 * Contains ranking scores that indicate the source's
 * overall credibility and authority.
 */
class SourceRankings
{
    /**
     * @param float|null $opr Overall Page Rank score
     */
    public function __construct(
        public readonly ?float $opr,
    ) {}

    /**
     * Create a SourceRankings instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            opr: isset($data['opr']) ? (float) $data['opr'] : null,
        );
    }
}
