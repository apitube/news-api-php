<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Machine translations of an article, keyed by target language.
 *
 * The block ships with every article. For English articles — and for
 * source languages without a translation model — its fields stay null.
 */
class Translations
{
    /**
     * @param Translation|null $en English translation of the headline and description
     */
    public function __construct(
        public readonly ?Translation $en,
    ) {}

    /**
     * Create a Translations instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            en: isset($data['en']) ? Translation::fromArray($data['en']) : null,
        );
    }
}
