<?php

use PHPUnit\Framework\TestCase;
use Jacobk\PhpTier2\Services\ShortLinkService;
use Jacobk\PhpTier2\Repositories\JsonShortLinkRepository;
use Jacobk\PhpTier2\Repositories\JsonShortLinkEventRepository;

class ShortLinkServiceTest extends TestCase {
    private string $shortlinksFile;
    private string $eventsFile;
    private ShortLinkService $service;

    protected function setUp(): void {
        $dir = __DIR__ . '/../../data/tests';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->shortlinksFile = $dir . '/test_shortlinks.json';
        $this->eventsFile     = $dir . '/test_events.json';
        file_put_contents($this->shortlinksFile, json_encode([]));
        file_put_contents($this->eventsFile, json_encode([]));
        $this->service = new ShortLinkService(
            new JsonShortLinkRepository($this->shortlinksFile),
            new JsonShortLinkEventRepository($this->eventsFile)
        );
    }


    protected function tearDown(): void {
        unlink($this->shortlinksFile);
        unlink($this->eventsFile);
    }

    public function testGenerateCodeLength() {
        $code = $this->service->generateCode(8);
        $this->assertEquals(8, strlen($code));
    }

    public function testGenerateCodeCharacters() {
        $code = $this->service->generateCode(10);
        $this->assertMatchesRegularExpression('/^[0-9a-zA-Z]+$/', $code);
    }

    public function testCreateShortLink() {
        $url = 'https://example.com';
        $linkId = 'test123';
        $record = $this->service->create($url, $linkId);
        $this->assertArrayHasKey('id', $record);
        $this->assertArrayHasKey('code', $record);
        $this->assertArrayHasKey('link_id', $record);
        $this->assertArrayHasKey('target_url', $record);
        $this->assertArrayHasKey('created_at', $record);
        $this->assertEquals($url, $record['target_url']);
        $this->assertEquals($linkId, $record['link_id']);
    }

    public function testResolveShortLink() {
        $url = 'https://example.com';
        $linkId = 'test123';
        $record = $this->service->create($url, $linkId);
        $resolved = $this->service->resolve($record['code']);
        $this->assertNotNull($resolved);
        $this->assertEquals($record['id'], $resolved['id']);
        $this->assertEquals($url, $resolved['target_url']);
    }

    public function testResolveNonExistentCode() {
        $resolved = $this->service->resolve('nonexistent');
        $this->assertNull($resolved);
    }

    public function testLogAndGetEvents() {
        $url = 'https://example.com';
        $linkId = 'test123';
        $record = $this->service->create($url, $linkId);
        $shortlinkId = $record['id'];

        $eventData1 = ['ip' => '192.168.1.1'];
        $this->service->logEvent($shortlinkId, $eventData1);

        $events = $this->service->getEvents($shortlinkId);
        $this->assertCount(1, $events);
    }
}