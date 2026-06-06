<?php

namespace Tests\Repositories;

use PHPUnit\Framework\TestCase;
use Jacobk\PhpTier2\Repositories\LinkRepositoryInterface;

abstract class LinkRepositoryContractTest extends TestCase
{
    abstract protected function repository(): LinkRepositoryInterface;

    public function testAddAndFindLink(): void
    {
        $repo = $this->repository();

        $record = $repo->add('https://example.com', [
            'title' => 'Example Site',
            'domain' => 'example.com'
        ]);

        $this->assertArrayHasKey('id', $record);

        $found = $repo->find($record['id']);
        $this->assertNotNull($found);

        $this->assertSame('Example Site', $found['title']);
        $this->assertSame('https://example.com', $found['url']);
        $this->assertSame('example.com', $found['domain']);
    }

    public function testMarkRead(): void
    {
        $repo = $this->repository();

        $record = $repo->add('https://example.com', []);

        $repo->markRead($record['id']);

        $found = $repo->find($record['id']);

        $this->assertTrue($found['read']);
    }

    public function testDelete(): void
    {
        $repo = $this->repository();

        $record = $repo->add('https://example.com', []);

        $repo->delete($record['id']);

        $found = $repo->find($record['id']);

        $this->assertNull($found);
    }

    public function testSearch(): void
    {
        $repo = $this->repository();

        $repo->add('https://search-example.com', ['title' => 'Example Site']);
        $repo->add('https://test.com', ['title' => 'Test Site']);

        $results = $repo->search('search');

        $this->assertCount(1, $results);
        $this->assertSame('Example Site', $results[0]['title']);
    }
}
