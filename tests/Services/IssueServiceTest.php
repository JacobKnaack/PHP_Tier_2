<?php
declare(strict_types=1);

namespace Tests\Services;

use Jacobk\PhpTier2\Model\IssueModel;
use Jacobk\PhpTier2\Repositories\IssueRepository;
use Jacobk\PhpTier2\Model\Schema;
use Jacobk\PhpTier2\Services\IssueService;
use PDO;
use PHPUnit\Framework\TestCase;

class IssueServiceTest extends TestCase
{
    private PDO $pdo;
    private IssueService $service;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $schema = new Schema($this->pdo);
        $model = new IssueModel($this->pdo);
        $repo = new IssueRepository($model, $schema);

        $repo->init();
        $this->service = new IssueService($repo);
    }

    public function testServiceCreate(): void
    {
        $issue = $this->service->create('SERVICE TITLE', 'SERVICE DESCRIPTION');
        $this->assertEquals('SERVICE TITLE', $issue['title']);
        $this->assertEquals('SERVICE DESCRIPTION', $issue['description']);
        $this->assertEquals('open', $issue['status']);
    }

    public function testServiceCreateRejectsInvalidTitle(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service->create('', 'missing title');
    }

    public function testGetsIssueFromService(): void
    {
        $issue = $this->service->create('TEST 2','DESCRIPTION 2');
        $found = $this->service->get($issue['id']);

        $this->assertEquals($issue['id'], $found['id']);
    }

    public function testGetIssueReturnsNullWhenMissing(): void
    {
        $found = $this->service->get(999);
        $this->assertNull($found);
    }

    public function testUpdateRejectsInvalidStatus(): void
    {
        $issue = $this->service->create('TEST 3', 'DESCRIPTION 3');
        $this->expectException(\InvalidArgumentException::class);

        $this->service->update($issue['id'], [
            'status' => 'BAD',
        ]);
    }

    public function testDeleteIssue(): void
    {
        $issue = $this->service->create('TEST 4', 'DESCRIPTION 4');
        $deleted = $this->service->delete($issue['id']);
        $this->assertTrue($deleted);
        $this->assertNull($this->service->get($issue['id']));
    }

    public function testListsAllIssues(): void
    {
        $this->service->create('Issue A', 'Description A');
        $this->service->create('Issue B', 'Description C');

        $list = $this->service->list();
        $this->assertCount(2, $list);
        $this->assertEquals('Issue A', $list[0]['title']);
        $this->assertEquals('Issue B', $list[1]['title']);
    }

    public function testFindByStatus(): void
    {
        $this->service->create('TEST 5', 'DESCRIPTION 5');
        $this->service->create('TEST 6', 'DESCRIPTION 6', 'closed');
        $this->service->create('TEST 7', 'DESCRIPTION 7');

        $openIssues = $this->service->findByStatus('open');
        $this->assertCount(2, $openIssues);
    }
}