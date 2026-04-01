<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Extractive summary sentence.
 *
 * Represents a single sentence extracted from the article
 * as part of its automatic summary, along with per-sentence
 * sentiment analysis.
 */
class Summary
{
    /**
     * @param string|null         $sentence  Extracted summary sentence text
     * @param SentimentValue|null $sentiment Sentiment analysis for this sentence
     */
    public function __construct(
        public readonly ?string $sentence,
        public readonly ?SentimentValue $sentiment,
    ) {}

    /**
     * Create a Summary instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sentence: $data['sentence'] ?? null,
            sentiment: isset($data['sentiment']) ? SentimentValue::fromArray($data['sentiment']) : null,
        );
    }
}
