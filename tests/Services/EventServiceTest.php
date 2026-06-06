<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Jacobk\PhpTier2\Services\EventService;
use Jacobk\PhpTier2\Repositories\JsonShortLinkEventRepository;

class EventServiceTest extends TestCase
{
    private string $dataDir;
    private string $eventsFile;

    private EventService $service;

    protected function setUp(): void
    {
        // Ensure test data directory exists
        $this->dataDir = __DIR__ . '/../../data/tests';
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0777, true);
        }

        // Fresh JSON file
        $this->eventsFile = $this->dataDir . '/test_shortlink_events.json';
        file_put_contents($this->eventsFile, json_encode([]));

        // Repository + service
        $repo = new JsonShortLinkEventRepository($this->eventsFile);
        $this->service = new EventService($repo);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->eventsFile)) {
            unlink($this->eventsFile);
        }
    }

    public function testLogCreatesEventRecord()
    {
        $shortlinkId = 'sl_123';

        $event = $this->service->log($shortlinkId, [
            'ip_hash' => 'abc123',
            'referrer' => 'https://google.com',
            'user_agent' => 'PHPUnit'
        ]);

        $this->assertArrayHasKey('id', $event);
        $this->assertEquals($shortlinkId, $event['shortlink_id']);
        $this->assertEquals('abc123', $event['ip_hash']);
        $this->assertEquals('https://google.com', $event['referrer']);
        $this->assertEquals('PHPUnit', $event['user_agent']);
    }

    public function testGetByShortlinkReturnsOnlyMatchingEvents()
    {
        $slA = 'sl_A';
        $slB = 'sl_B';

        $this->service->log($slA, ['ip_hash' => '111']);
        $this->service->log($slA, ['ip_hash' => '222']);
        $this->service->log($slB, ['ip_hash' => '333']);

        $eventsA = $this->service->getByShortlink($slA);

        $this->assertCount(2, $eventsA);
        $this->assertEquals('111', $eventsA[0]['ip_hash']);
        $this->assertEquals('222', $eventsA[1]['ip_hash']);
    }

    public function testDeleteByShortlinkRemovesEvents()
    {
        $sl = 'sl_123';

        $this->service->log($sl, ['ip_hash' => 'aaa']);
        $this->service->log($sl, ['ip_hash' => 'bbb']);

        // Delete all events for this shortlink
        $this->service->deleteByShortlink($sl);

        $remaining = $this->service->getByShortlink($sl);

        $this->assertCount(0, $remaining);
    }
}
