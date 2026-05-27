<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Jacobk\PhpTier2\Services\LinkService;

class LinkServiceTest extends TestCase {
    private string $dataDir;
    private string $linksFile;
    private LinkService $service;

    protected function setup(): void {
        // ensure test data directory exists
        $this->dataDir = __DIR__ . '/../../data/tests';
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0777, true);
        }

        $this->linksFile = $this->dataDir . '/test_links.json';
        file_put_contents($this->linksFile, json_encode([]));
        $this->service = new LinkService($this->linksFile);
    }

    protected function tearDown(): void {
        if (file_exists($this->linksFile)) {
            unlink($this->linksFile);
        }
    }

    public function testAddLinkCreatesEntry()
    {
        $this->service->add('https://example.com', [
            'title' => 'Example',
            'favicon' => 'https://example.com/favicon.ico',
            'domain' => 'example.com'
        ]);

        $links = $this->service->all();
        $this->assertCount(1, $links);
        $this->assertEquals('https://example.com', $links[0]['url']);
        $this->assertEquals('Example', $links[0]['title']);
        $this->assertEquals('https://example.com/favicon.ico', $links[0]['favicon']);
        $this->assertEquals('example.com', $links[0]['domain']);
        $this->assertFalse($links[0]['read']);
    }

    public function testFindReturnsCorrectLink()
    {
        $this->service->add('https://a.com', ['title' => 'A']);
        $this->service->add('https://b.com', ['title' => 'B']);

        $links = $this->service->all();
        $targetId = $links[1]['id'];
        $found = $this->service->find($targetId);
        $this->assertNotNull($found);
        $this->assertEquals('https://b.com', $found['url']);
    }

    public function testFindReturnsNullForNonexistentId()
    {
        $this->service->add('https://a.com', ['title' => 'A']);
        $result = $this->service->find('nonexistent-id');
        $this->assertNull($result);
    }

    public function testMarkReadUpdatedLink()
    {
        $this->service->add('https://example.com', ['title' => 'Example']);
        $link = $this->service->all()[0];

        $this->service->markRead($link['id']);

        $updated = $this->service->find($link['id']);
        $this->assertTrue($updated['read']);
    }

    public function testDeleteRemovesLink()
    {
        $this->service->add('https://a.com', ['title' => 'A']);
        $this->service->add('https://b.com', ['title' => 'B']);

        $links = $this->service->all();
        $deleteId = $links[0]['id'];

        $this->service->delete($deleteId);

        $remaining = $this->service->all();
        $this->assertCount(1, $remaining);
        $this->assertNotEquals($deleteId, $remaining[0]['id']);
    }

    public function testSearchFindsMatchingLinks()
    {
        $this->service->add('https://example.com', ['title' => 'Example']);
        $this->service->add('https://google.com', ['title' => 'Google']);
        $this->service->add('https://github.com', ['title' => 'GitHub']);

        $results = $this->service->search('goo');

        $this->assertCount(1, $results);
        $this->assertEquals('Google', $results[0]['title']);
    }

    public function testSearchIsCaseInsensitive()
    {
        $this->service->add('https://example.com', ['title' => 'Example']);
        $results = $this->service->search('EXA');

        $this->assertCount(1, $results);
        $this->assertEquals('Example', $results[0]['title']);
    }

    public function testSearchReturnsEmptyArrayWhenNoMatches()
    {
        $this->service->add('https://example.com', ['title' => 'Example']);
        $results = $this->service->search('nomatch');

        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }
}