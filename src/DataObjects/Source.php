<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * News source (publisher) information.
 *
 * Contains metadata about the website or publication
 * that originally published the article, including
 * domain, media bias rating, and rankings.
 */
class Source
{
    /**
     * @param string|null          $id          Unique source identifier
     * @param string|null          $domain      Source domain name (e.g. 'reuters.com')
     * @param string|null          $homePageUrl Source home page URL
     * @param string|null          $type        Source type (e.g. 'newspaper', 'blog', 'agency')
     * @param string|null          $bias        Media bias rating (e.g. 'center', 'left', 'right')
     * @param SourceRankings|null  $rankings    Source authority and ranking metrics
     * @param SourceLocation|null  $location    Geographic location of the source
     * @param string|null          $favicon     Source favicon URL
     */
    public function __construct(
        public readonly ?string $id,
        public readonly ?string $domain,
        public readonly ?string $homePageUrl,
        public readonly ?string $type,
        public readonly ?string $bias,
        public readonly ?SourceRankings $rankings,
        public readonly ?SourceLocation $location,
        public readonly ?string $favicon,
    ) {}

    /**
     * Create a Source instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            domain: $data['domain'] ?? null,
            homePageUrl: $data['home_page_url'] ?? null,
            type: $data['type'] ?? null,
            bias: $data['bias'] ?? null,
            rankings: isset($data['rankings']) ? SourceRankings::fromArray($data['rankings']) : null,
            location: isset($data['location']) ? SourceLocation::fromArray($data['location']) : null,
            favicon: $data['favicon'] ?? null,
        );
    }
}
