<?php

declare(strict_types=1);

namespace APITube\Responses;

/**
 * Paginated list of news articles returned by the APITube API.
 *
 * Contains the article results along with pagination metadata,
 * facets, highlighting, and other response-level information.
 */
class ArticleList
{
    /**
     * @param Article[]            $articles        List of article objects
     * @param string               $status          Response status (e.g. 'ok')
     * @param int                  $page            Current page number
     * @param int                  $limit           Maximum articles per page
     * @param bool                 $hasNextPages    Whether more pages are available
     * @param string|null          $nextPage        URL or cursor for the next page
     * @param bool                 $hasPreviousPage Whether a previous page exists
     * @param string|null          $previousPage    URL or cursor for the previous page
     * @param string|null          $path            Request path
     * @param array|null           $export          Export options/URLs
     * @param string|null          $requestId       Unique request identifier for debugging
     * @param array|null           $facets          Aggregation facets for filtering
     * @param array|null           $highlighting    Search term highlighting data
     * @param array|null           $meta            Additional response metadata
     * @param array|null           $headlines       Headline aggregation data
     * @param array|null           $userInput       Parsed user input parameters
     */
    public function __construct(
        public readonly array $articles,
        public readonly string $status,
        public readonly int $page,
        public readonly int $limit,
        public readonly bool $hasNextPages,
        public readonly ?string $nextPage,
        public readonly bool $hasPreviousPage,
        public readonly ?string $previousPage,
        public readonly ?string $path,
        public readonly ?array $export,
        public readonly ?string $requestId,
        public readonly ?array $facets,
        public readonly ?array $highlighting,
        public readonly ?array $meta,
        public readonly ?array $headlines,
        public readonly ?array $userInput,
    ) {}

    /**
     * Create an ArticleList instance from an API response array.
     *
     * Maps the 'results' key to Article objects and extracts pagination metadata.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $export = $data['export'] ?? null;

        return new self(
            articles: array_map(
                fn(array $item) => Article::fromArray($item),
                $data['results'] ?? [],
            ),
            status: $data['status'] ?? 'ok',
            page: (int) ($data['page'] ?? 1),
            limit: (int) ($data['limit'] ?? 100),
            hasNextPages: (bool) ($data['has_next_pages'] ?? $data['has_next_page'] ?? false),
            nextPage: $data['next_page'] ?? null,
            hasPreviousPage: (bool) ($data['has_previous_page'] ?? false),
            previousPage: $data['previous_page'] ?? null,
            path: $data['path'] ?? null,
            export: is_array($export) ? $export : null,
            requestId: $data['request_id'] ?? null,
            facets: $data['facets'] ?? null,
            highlighting: $data['highlighting'] ?? null,
            meta: $data['meta'] ?? null,
            headlines: $data['headlines'] ?? null,
            userInput: $data['user_input'] ?? null,
        );
    }
}
