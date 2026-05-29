<?php

declare(strict_types=1);

namespace APITube\Responses;

/**
 * Paginated list of reference entities (people, companies, sources, journalists).
 *
 * Wraps the pagination metadata in typed properties while keeping each result
 * item as a raw associative array, since reference payloads vary by entity type.
 */
class ReferenceList
{
    /**
     * @param array<int, array<string, mixed>> $results      List of reference items (raw arrays)
     * @param string                           $status       Response status (e.g. 'ok')
     * @param int                              $page         Current page number
     * @param int                              $limit        Maximum items per page
     * @param bool                             $hasNextPages Whether more pages are available
     */
    public function __construct(
        public readonly array $results,
        public readonly string $status,
        public readonly int $page,
        public readonly int $limit,
        public readonly bool $hasNextPages,
    ) {}

    /**
     * Create a ReferenceList instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            results: is_array($data['results'] ?? null) ? $data['results'] : [],
            status: $data['status'] ?? 'ok',
            page: (int) ($data['page'] ?? 1),
            limit: (int) ($data['limit'] ?? 100),
            hasNextPages: (bool) ($data['has_next_pages'] ?? $data['has_next_page'] ?? false),
        );
    }
}
