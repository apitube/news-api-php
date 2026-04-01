<?php

declare(strict_types=1);

namespace APITube\Responses;

/**
 * Account balance and subscription information.
 *
 * Returned by the /v1/balance endpoint with the current
 * API key details, remaining points, and active plan.
 */
class BalanceResponse
{
    /**
     * @param string|null $apiKey API key associated with this account
     * @param int         $points Remaining API usage points
     * @param string|null $plan   Active subscription plan name
     */
    public function __construct(
        public readonly ?string $apiKey,
        public readonly int $points,
        public readonly ?string $plan,
    ) {}

    /**
     * Create a BalanceResponse from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            apiKey: $data['api_key'] ?? $data['apiKey'] ?? null,
            points: (int) ($data['points'] ?? 0),
            plan: $data['plan'] ?? null,
        );
    }
}
