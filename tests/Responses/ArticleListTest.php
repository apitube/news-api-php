<?php

declare(strict_types=1);

namespace APITube\Tests\Responses;

use APITube\Responses\Article;
use APITube\Responses\ArticleList;
use PHPUnit\Framework\TestCase;

class ArticleListTest extends TestCase
{
    public function test_from_array_maps_results_to_articles(): void
    {
        $data = [
            'status' => 'ok',
            'results' => [
                ['id' => 'art-1', 'title' => 'First'],
                ['id' => 'art-2', 'title' => 'Second'],
                ['id' => 'art-3', 'title' => 'Third'],
            ],
            'page' => 2,
            'limit' => 100,
            'has_next_pages' => true,
            'next_page' => 'https://api.apitube.io/v1/news/everything?page=3',
            'has_previous_page' => true,
            'previous_page' => 'https://api.apitube.io/v1/news/everything?page=1',
            'path' => '/v1/news/everything',
            'export' => [
                'json' => 'https://api.apitube.io/export/123.json',
                'csv' => 'https://api.apitube.io/export/123.csv',
            ],
            'request_id' => 'req-456',
            'facets' => ['source.id' => [['value' => 'src-1', 'count' => 10]]],
            'highlighting' => ['art-1' => ['title' => ['<em>First</em>']]],
            'meta' => ['total' => 300],
            'headlines' => ['Breaking: Major Event', 'Tech Update'],
            'user_input' => ['query' => 'test', 'filters' => []],
        ];

        $list = ArticleList::fromArray($data);

        $this->assertCount(3, $list->articles);
        $this->assertContainsOnlyInstancesOf(Article::class, $list->articles);
        $this->assertSame('art-1', $list->articles[0]->id);
        $this->assertSame('Second', $list->articles[1]->title);
        $this->assertSame('ok', $list->status);
        $this->assertSame(2, $list->page);
        $this->assertSame(100, $list->limit);
        $this->assertTrue($list->hasNextPages);
        $this->assertSame('https://api.apitube.io/v1/news/everything?page=3', $list->nextPage);
        $this->assertTrue($list->hasPreviousPage);
        $this->assertSame('https://api.apitube.io/v1/news/everything?page=1', $list->previousPage);
        $this->assertSame('/v1/news/everything', $list->path);
        $this->assertIsArray($list->export);
        $this->assertSame('https://api.apitube.io/export/123.json', $list->export['json']);
        $this->assertSame('req-456', $list->requestId);
        $this->assertIsArray($list->facets);
        $this->assertIsArray($list->highlighting);
        $this->assertIsArray($list->meta);
        $this->assertSame(['Breaking: Major Event', 'Tech Update'], $list->headlines);
        $this->assertSame(['query' => 'test', 'filters' => []], $list->userInput);
    }

    public function test_from_array_empty_results(): void
    {
        $list = ArticleList::fromArray([
            'results' => [],
            'page' => 1,
            'has_next_pages' => false,
        ]);

        $this->assertCount(0, $list->articles);
        $this->assertSame('ok', $list->status);
        $this->assertSame(1, $list->page);
        $this->assertSame(100, $list->limit);
        $this->assertFalse($list->hasNextPages);
        $this->assertNull($list->export);
        $this->assertNull($list->requestId);
        $this->assertNull($list->facets);
    }

    public function test_from_array_missing_results_key(): void
    {
        $list = ArticleList::fromArray([]);

        $this->assertCount(0, $list->articles);
        $this->assertSame(1, $list->page);
        $this->assertFalse($list->hasNextPages);
    }
}
