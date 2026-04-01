<?php

declare(strict_types=1);

namespace APITube\Tests\Responses;

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
use APITube\Responses\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    public function test_from_array_full(): void
    {
        $data = [
            'id' => 123,
            'title' => 'AI Revolution',
            'description' => 'An article about AI',
            'body' => 'Full article body here...',
            'body_html' => '<p>Full article body here...</p>',
            'href' => 'https://example.com/ai-revolution',
            'image' => 'https://example.com/image.jpg',
            'published_at' => '2026-01-15T10:30:00Z',
            'language' => 'en',
            'source' => [
                'id' => 'src-1',
                'domain' => 'example.com',
                'home_page_url' => 'https://example.com',
                'type' => 'news',
                'bias' => 'center',
                'rankings' => ['opr' => 85.5],
                'location' => ['country_name' => 'United States', 'country_code' => 'US'],
                'favicon' => 'https://example.com/favicon.ico',
            ],
            'author' => ['id' => 'auth-1', 'name' => 'John Doe'],
            'categories' => [
                ['id' => 'cat-1', 'name' => 'Technology', 'score' => 0.95, 'taxonomy' => 'iab', 'links' => ['self' => '/categories/cat-1']],
            ],
            'topics' => [
                ['id' => 'top-1', 'name' => 'Artificial Intelligence', 'score' => 0.9, 'links' => ['self' => '/topics/top-1']],
            ],
            'industries' => [
                ['id' => 'ind-1', 'name' => 'Software', 'links' => ['self' => '/industries/ind-1']],
            ],
            'entities' => [
                [
                    'id' => 'ent-1',
                    'name' => 'OpenAI',
                    'type' => 'organization',
                    'frequency' => 5,
                    'title' => ['pos' => [0, 15]],
                    'body' => ['pos' => [10, 45, 120]],
                    'links' => ['self' => '/entities/ent-1', 'wikipedia' => 'https://en.wikipedia.org/wiki/OpenAI', 'wikidata' => 'https://www.wikidata.org/wiki/Q21055863'],
                    'metadata' => ['founded' => '2015'],
                ],
            ],
            'locations_mentioned' => [
                ['name' => 'San Francisco', 'country' => 'US', 'lat' => 37.77, 'lng' => -122.42, 'type' => 'city'],
            ],
            'sentiment' => [
                'overall' => ['score' => 0.8, 'polarity' => 'positive'],
                'title' => ['score' => 0.7, 'polarity' => 'positive'],
                'body' => ['score' => 0.85, 'polarity' => 'positive'],
            ],
            'readability' => [
                'flesch_kincaid_grade' => 12.5,
                'flesch_reading_ease' => 45.2,
                'automated_readability_index' => 14.5,
                'difficulty_level' => 'advanced',
                'target_audience' => 'professional',
                'reading_age' => 18.0,
                'avg_words_per_sentence' => 22.5,
                'avg_syllables_per_word' => 1.8,
            ],
            'summary' => [
                ['sentence' => 'AI is transforming the world.', 'sentiment' => ['score' => 0.9, 'polarity' => 'positive']],
            ],
            'shares' => ['total' => 1500, 'facebook' => 800, 'twitter' => 500, 'reddit' => 200],
            'media' => [
                ['url' => 'https://example.com/video.mp4', 'type' => 'video'],
            ],
            'links' => [
                ['url' => 'https://related.com/article', 'type' => 'related'],
            ],
            'story' => ['id' => 'story-1', 'uri' => 'https://example.com/story/1'],
            'keywords' => ['AI', 'machine learning', 'technology'],
            'is_duplicate' => false,
            'is_free' => true,
            'is_breaking' => false,
            'read_time' => 5,
            'sentences_count' => 42,
            'paragraphs_count' => 8,
            'words_count' => 1200,
            'characters_count' => 7500,
        ];

        $article = Article::fromArray($data);

        // Scalar fields
        $this->assertSame('123', $article->id);
        $this->assertSame('AI Revolution', $article->title);
        $this->assertSame('An article about AI', $article->description);
        $this->assertSame('Full article body here...', $article->body);
        $this->assertSame('<p>Full article body here...</p>', $article->bodyHtml);
        $this->assertSame('https://example.com/ai-revolution', $article->url);
        $this->assertSame('https://example.com/image.jpg', $article->image);
        $this->assertSame('2026-01-15T10:30:00Z', $article->publishedAt);
        $this->assertSame('en', $article->language);

        // Source (nested)
        $this->assertInstanceOf(Source::class, $article->source);
        $this->assertSame('example.com', $article->source->domain);
        $this->assertSame('https://example.com', $article->source->homePageUrl);
        $this->assertSame(85.5, $article->source->rankings->opr);
        $this->assertSame('US', $article->source->location->countryCode);

        // Author
        $this->assertInstanceOf(Author::class, $article->author);
        $this->assertSame('John Doe', $article->author->name);

        // Categories
        $this->assertCount(1, $article->categories);
        $this->assertInstanceOf(Category::class, $article->categories[0]);
        $this->assertSame('Technology', $article->categories[0]->name);
        $this->assertSame(0.95, $article->categories[0]->score);
        $this->assertSame(['self' => '/categories/cat-1'], $article->categories[0]->links);

        // Topics
        $this->assertCount(1, $article->topics);
        $this->assertInstanceOf(Topic::class, $article->topics[0]);
        $this->assertSame('Artificial Intelligence', $article->topics[0]->name);
        $this->assertSame(['self' => '/topics/top-1'], $article->topics[0]->links);

        // Industries
        $this->assertCount(1, $article->industries);
        $this->assertInstanceOf(Industry::class, $article->industries[0]);
        $this->assertSame('Software', $article->industries[0]->name);
        $this->assertSame(['self' => '/industries/ind-1'], $article->industries[0]->links);

        // Entities
        $this->assertCount(1, $article->entities);
        $this->assertInstanceOf(Entity::class, $article->entities[0]);
        $this->assertSame('OpenAI', $article->entities[0]->name);
        $this->assertSame(5, $article->entities[0]->frequency);
        $this->assertSame([0, 15], $article->entities[0]->titlePositions);
        $this->assertSame([10, 45, 120], $article->entities[0]->bodyPositions);
        $this->assertSame('https://en.wikipedia.org/wiki/OpenAI', $article->entities[0]->links['wikipedia']);
        $this->assertSame(['founded' => '2015'], $article->entities[0]->metadata);

        // Locations (from locations_mentioned)
        $this->assertCount(1, $article->locations);
        $this->assertInstanceOf(Location::class, $article->locations[0]);
        $this->assertSame('San Francisco', $article->locations[0]->name);
        $this->assertSame(37.77, $article->locations[0]->lat);

        // Sentiment (deep nesting)
        $this->assertInstanceOf(Sentiment::class, $article->sentiment);
        $this->assertSame(0.8, $article->sentiment->overall->score);
        $this->assertSame('positive', $article->sentiment->overall->polarity);
        $this->assertSame(0.7, $article->sentiment->title->score);

        // Readability
        $this->assertInstanceOf(Readability::class, $article->readability);
        $this->assertSame(12.5, $article->readability->fleschKincaidGrade);
        $this->assertSame(45.2, $article->readability->fleschReadingEase);
        $this->assertSame(14.5, $article->readability->automatedReadabilityIndex);
        $this->assertSame('advanced', $article->readability->difficultyLevel);
        $this->assertSame('professional', $article->readability->targetAudience);
        $this->assertSame(18.0, $article->readability->readingAge);
        $this->assertSame(22.5, $article->readability->avgWordsPerSentence);
        $this->assertSame(1.8, $article->readability->avgSyllablesPerWord);

        // Summary
        $this->assertCount(1, $article->summary);
        $this->assertInstanceOf(Summary::class, $article->summary[0]);
        $this->assertSame('AI is transforming the world.', $article->summary[0]->sentence);
        $this->assertSame('positive', $article->summary[0]->sentiment->polarity);

        // Share (from shares key)
        $this->assertInstanceOf(Share::class, $article->share);
        $this->assertSame(1500, $article->share->total);
        $this->assertSame(800, $article->share->facebook);

        // Media
        $this->assertCount(1, $article->media);
        $this->assertInstanceOf(Media::class, $article->media[0]);
        $this->assertSame('video', $article->media[0]->type);

        // Links
        $this->assertCount(1, $article->links);
        $this->assertInstanceOf(Link::class, $article->links[0]);
        $this->assertSame('related', $article->links[0]->type);

        // Story
        $this->assertInstanceOf(Story::class, $article->story);
        $this->assertSame('story-1', $article->story->id);

        // New fields
        $this->assertSame(['AI', 'machine learning', 'technology'], $article->keywords);
        $this->assertFalse($article->isDuplicate);
        $this->assertTrue($article->isFree);
        $this->assertFalse($article->isBreaking);
        $this->assertSame(5, $article->readTime);
        $this->assertSame(42, $article->sentencesCount);
        $this->assertSame(8, $article->paragraphsCount);
        $this->assertSame(1200, $article->wordsCount);
        $this->assertSame(7500, $article->charactersCount);
    }

    public function test_from_array_minimal(): void
    {
        $article = Article::fromArray([
            'id' => 'art-min',
            'title' => 'Minimal article',
        ]);

        $this->assertSame('art-min', $article->id);
        $this->assertSame('Minimal article', $article->title);
        $this->assertNull($article->description);
        $this->assertNull($article->body);
        $this->assertNull($article->bodyHtml);
        $this->assertNull($article->source);
        $this->assertNull($article->sentiment);
        $this->assertNull($article->categories);
        $this->assertNull($article->readability);
        $this->assertNull($article->keywords);
        $this->assertNull($article->isDuplicate);
        $this->assertNull($article->readTime);
    }

    public function test_from_array_empty(): void
    {
        $article = Article::fromArray([]);

        $this->assertNull($article->id);
        $this->assertNull($article->title);
        $this->assertNull($article->source);
    }
}
