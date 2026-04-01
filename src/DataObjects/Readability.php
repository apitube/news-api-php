<?php

declare(strict_types=1);

namespace APITube\DataObjects;

/**
 * Text readability analysis metrics.
 *
 * Contains Flesch-Kincaid and other readability scores
 * computed for the article body text, along with derived
 * difficulty level and target audience information.
 */
class Readability
{
    /**
     * @param float|null  $fleschKincaidGrade        Flesch-Kincaid grade level (US school grade)
     * @param float|null  $fleschReadingEase         Flesch reading ease score (0–100, higher = easier)
     * @param float|null  $automatedReadabilityIndex Automated Readability Index score
     * @param string|null $difficultyLevel           Human-readable difficulty (e.g. 'easy', 'medium', 'hard')
     * @param string|null $targetAudience            Intended audience description
     * @param float|null  $readingAge                Estimated minimum reading age
     * @param float|null  $avgWordsPerSentence       Average number of words per sentence
     * @param float|null  $avgSyllablesPerWord       Average number of syllables per word
     */
    public function __construct(
        public readonly ?float $fleschKincaidGrade,
        public readonly ?float $fleschReadingEase,
        public readonly ?float $automatedReadabilityIndex,
        public readonly ?string $difficultyLevel,
        public readonly ?string $targetAudience,
        public readonly ?float $readingAge,
        public readonly ?float $avgWordsPerSentence,
        public readonly ?float $avgSyllablesPerWord,
    ) {}

    /**
     * Create a Readability instance from an API response array.
     *
     * @param array<string, mixed> $data Raw API response data
     *
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            fleschKincaidGrade: isset($data['flesch_kincaid_grade']) ? (float) $data['flesch_kincaid_grade'] : null,
            fleschReadingEase: isset($data['flesch_reading_ease']) ? (float) $data['flesch_reading_ease'] : null,
            automatedReadabilityIndex: isset($data['automated_readability_index']) ? (float) $data['automated_readability_index'] : null,
            difficultyLevel: $data['difficulty_level'] ?? null,
            targetAudience: $data['target_audience'] ?? null,
            readingAge: isset($data['reading_age']) ? (float) $data['reading_age'] : null,
            avgWordsPerSentence: isset($data['avg_words_per_sentence']) ? (float) $data['avg_words_per_sentence'] : null,
            avgSyllablesPerWord: isset($data['avg_syllables_per_word']) ? (float) $data['avg_syllables_per_word'] : null,
        );
    }
}
