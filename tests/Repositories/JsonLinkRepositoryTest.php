<?php

namespace Tests\Repositories;

use Jacobk\PhpTier2\Repositories\JsonLinkRepository;
use Jacobk\PhpTier2\Repositories\LinkRepositoryInterface;

class JsonLinkRepositoryTest extends LinkRepositoryContractTest
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = __DIR__ . '/test_links.json';
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    protected function repository(): LinkRepositoryInterface
    {
        return new JsonLinkRepository($this->testFile);
    }
}
