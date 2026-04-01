<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Individual sentiment measurement.
 *
 * Holds a numeric sentiment score and its polarity classification
 * (e.g. 'positive', 'negative', 'neutral').
 */
class SentimentValue
{
    /**
     * @param float|null  $score    Sentiment score (typically -1.0 to 1.0)
     * @param string|null $polarity Polarity label ('positive', 'negative', 'neutral')
     */
    public function __construct(
        public readonly ?float $score,
        public readonly ?string $polarity,
    ) {}

    /**
     * Create a SentimentValue instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            score: isset($data['score']) ? (float) $data['score'] : null,
            polarity: $data['polarity'] ?? null,
        );
    }
}
