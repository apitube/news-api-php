<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Article sentiment analysis results.
 *
 * Contains sentiment scores for the overall article,
 * its title, and body separately.
 */
class Sentiment
{
    /**
     * @param SentimentValue|null $overall Combined sentiment for the entire article
     * @param SentimentValue|null $title   Sentiment analysis of the article title
     * @param SentimentValue|null $body    Sentiment analysis of the article body
     */
    public function __construct(
        public readonly ?SentimentValue $overall,
        public readonly ?SentimentValue $title,
        public readonly ?SentimentValue $body,
    ) {}

    /**
     * Create a Sentiment instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            overall: isset($data['overall']) ? SentimentValue::fromArray($data['overall']) : null,
            title: isset($data['title']) ? SentimentValue::fromArray($data['title']) : null,
            body: isset($data['body']) ? SentimentValue::fromArray($data['body']) : null,
        );
    }
}
