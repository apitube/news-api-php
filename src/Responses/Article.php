<?php

declare(strict_types=1);

namespace APITube\Responses;

use APITube\DataObjects\Author;
use APITube\DataObjects\Category;
use APITube\DataObjects\Entity;
use APITube\DataObjects\Industry;
use APITube\DataObjects\Link;
use APITube\DataObjects\Location;
use APITube\DataObjects\Media;
use APITube\DataObjects\Readability;
use APITube\DataObjects\Sentiment;
use APITube\DataObjects\Share;
use APITube\DataObjects\Source;
use APITube\DataObjects\Story;
use APITube\DataObjects\Summary;
use APITube\DataObjects\Topic;

/**
 * Single news article returned by the APITube API.
 *
 * Contains the article content, metadata, NLP analysis results
 * (sentiment, readability, entities), and social share counts.
 */
class Article
{
    /**
     * @param string|null         $id              Unique article identifier
     * @param string|null         $title           Article headline
     * @param string|null         $description     Short article summary / lead paragraph
     * @param string|null         $body            Full article text (plain text)
     * @param string|null         $bodyHtml        Full article text (HTML)
     * @param string|null         $url             Canonical article URL
     * @param string|null         $image           Primary image URL
     * @param string|null         $publishedAt     ISO 8601 publication timestamp
     * @param string|null         $language        ISO 639-1 language code
     * @param Source|null         $source          News source details
     * @param Author|null         $author          Article author
     * @param Category[]|null     $categories      Article categories with relevance scores
     * @param Topic[]|null        $topics          Detected topics with relevance scores
     * @param Industry[]|null     $industries      Related industries
     * @param Entity[]|null       $entities        Named entities (people, orgs, locations, etc.)
     * @param Location[]|null     $locations       Locations mentioned in the article
     * @param Sentiment|null      $sentiment       Sentiment analysis results
     * @param Readability|null    $readability     Readability metrics
     * @param Summary[]|null      $summary         Extractive summary sentences with sentiment
     * @param Share|null          $share           Social media share counts
     * @param Media[]|null        $media           Attached media (images, videos)
     * @param Link[]|null         $links           Related links
     * @param Story|null          $story           Parent story cluster
     * @param string[]|null       $keywords        Extracted keywords
     * @param bool|null           $isDuplicate     Whether this article is a duplicate
     * @param bool|null           $isFree          Whether the article is freely accessible
     * @param bool|null           $isBreaking      Whether this is breaking news
     * @param int|null            $readTime        Estimated reading time in seconds
     * @param int|null            $sentencesCount  Number of sentences
     * @param int|null            $paragraphsCount Number of paragraphs
     * @param int|null            $wordsCount      Number of words
     * @param int|null            $charactersCount Number of characters
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly ?string $body,
        public readonly ?string $bodyHtml,
        public readonly ?string $url,
        public readonly ?string $image,
        public readonly ?string $publishedAt,
        public readonly ?string $language,
        public readonly ?Source $source,
        public readonly ?Author $author,
        public readonly ?array $categories,
        public readonly ?array $topics,
        public readonly ?array $industries,
        public readonly ?array $entities,
        public readonly ?array $locations,
        public readonly ?Sentiment $sentiment,
        public readonly ?Readability $readability,
        public readonly ?array $summary,
        public readonly ?Share $share,
        public readonly ?array $media,
        public readonly ?array $links,
        public readonly ?Story $story,
        public readonly ?array $keywords,
        public readonly ?bool $isDuplicate,
        public readonly ?bool $isFree,
        public readonly ?bool $isBreaking,
        public readonly ?int $readTime,
        public readonly ?int $sentencesCount,
        public readonly ?int $paragraphsCount,
        public readonly ?int $wordsCount,
        public readonly ?int $charactersCount,
    ) {}

    /**
     * Create an Article instance from an API response array.
     *
     * Maps snake_case API fields to camelCase properties, handling
     * alternative field names (e.g. 'href'/'url', 'shares'/'share',
     * 'locations_mentioned'/'locations').
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $shares = $data['shares'] ?? $data['share'] ?? null;
        $locations = $data['locations_mentioned'] ?? $data['locations'] ?? null;

        return new self(
            id: isset($data['id']) ? (string) $data['id'] : null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            body: $data['body'] ?? null,
            bodyHtml: $data['body_html'] ?? null,
            url: $data['href'] ?? $data['url'] ?? null,
            image: $data['image'] ?? null,
            publishedAt: $data['published_at'] ?? null,
            language: $data['language'] ?? null,
            source: isset($data['source']) ? Source::fromArray($data['source']) : null,
            author: isset($data['author']) ? Author::fromArray($data['author']) : null,
            categories: isset($data['categories'])
                ? array_map(fn(array $item) => Category::fromArray($item), $data['categories'])
                : null,
            topics: isset($data['topics'])
                ? array_map(fn(array $item) => Topic::fromArray($item), $data['topics'])
                : null,
            industries: isset($data['industries'])
                ? array_map(fn(array $item) => Industry::fromArray($item), $data['industries'])
                : null,
            entities: isset($data['entities'])
                ? array_map(fn(array $item) => Entity::fromArray($item), $data['entities'])
                : null,
            locations: is_array($locations)
                ? array_map(fn(array $item) => Location::fromArray($item), $locations)
                : null,
            sentiment: isset($data['sentiment']) ? Sentiment::fromArray($data['sentiment']) : null,
            readability: isset($data['readability']) ? Readability::fromArray($data['readability']) : null,
            summary: isset($data['summary'])
                ? array_map(fn(array $item) => Summary::fromArray($item), $data['summary'])
                : null,
            share: is_array($shares) ? Share::fromArray($shares) : null,
            media: isset($data['media'])
                ? array_map(fn(array $item) => Media::fromArray($item), $data['media'])
                : null,
            links: isset($data['links'])
                ? array_map(fn(array $item) => Link::fromArray($item), $data['links'])
                : null,
            story: isset($data['story']) ? Story::fromArray($data['story']) : null,
            keywords: $data['keywords'] ?? null,
            isDuplicate: isset($data['is_duplicate']) ? (bool) $data['is_duplicate'] : null,
            isFree: isset($data['is_free']) ? (bool) $data['is_free'] : null,
            isBreaking: isset($data['is_breaking']) ? (bool) $data['is_breaking'] : null,
            readTime: isset($data['read_time']) ? (int) $data['read_time'] : null,
            sentencesCount: isset($data['sentences_count']) ? (int) $data['sentences_count'] : null,
            paragraphsCount: isset($data['paragraphs_count']) ? (int) $data['paragraphs_count'] : null,
            wordsCount: isset($data['words_count']) ? (int) $data['words_count'] : null,
            charactersCount: isset($data['characters_count']) ? (int) $data['characters_count'] : null,
        );
    }
}
