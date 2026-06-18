<?php
declare(strict_types=1);

namespace Tests\Repositories;

use Jacobk\PhpTier2\Model\IssueModel;
use Jacobk\PhpTier2\Repositories\IssueRepository;
use Jacobk\PhpTier2\Model\Schema;
use PDO;
use PHPUnit\Framework\TestCase;

class IssueRepositoryTest extends TestCase
{
    private PDO $pdo;
    private IssueRepository $repo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $schema = new Schema($this->pdo);
        $model = new IssueModel($this->pdo);

        $this->repo = new IssueRepository($model, $schema);
        $this->repo->init();
    }

    public function testCreateIssue(): void
    {
        $id = $this->repo->createIssue(
            'TEST TITLE',
            'TEST DESCRIPTION'
        );
        $this->assertIsInt($id);
    }

    public function testEnumConstraintRejectsInvalidStatus(): void
    {
        $this->expectException(\PDOException::class);

        $this->repo->createIssue('TEST 1', 'TEST 1 DESCRIPTION', 'not-a-status');
    }

    public function testFindIssueAfterCreate(): void
    {
        $id = $this->repo->createIssue(
            'TEST FIND',
            'FIND THIS ISSUE'
        );
        $issue = $this->repo->getIssue($id);
        $this->assertNotNull($issue);
        $this->assertEquals('TEST FIND', $issue['title']);
        $this->assertEquals('FIND THIS ISSUE', $issue['description']);
        $this->assertEquals('open', $issue['status']);
    }

    public function testGetIssueReturnsNullForMissingId(): void
    {
        $issue = $this->repo->getIssue(999);
        $this->assertNull($issue);
    }

    public function testUpdateIssue(): void
    {
        $id = $this->repo->createIssue(
            'TEST UPDATE',
            'UPDATE THIS ISSUE',
        );
        $this->repo->updateIssue($id, [
            'status' => 'closed',
        ]);
        $issue = $this->repo->getIssue($id);
        $this->assertEquals('closed', $issue['status']);
    }

    public function testListIssues(): void
    {
        $this->repo->createIssue('TEST 1', 'TEST 1 DESCRIPTION');
        $this->repo->createIssue('TEST 2', 'TEST 2 DESCRIPTION');
        $this->repo->createIssue('TEST 3', 'TEST 3 DESCRIPTION');

        $issues = $this->repo->listIssues();
        $this->assertCount(3, $issues);
    }

    public function testFindByStatus(): void
    {
        $this->repo->createIssue('TEST 1', 'TEST 1 DESCRIPTION');
        $this->repo->createIssue('TEST 2', 'TEST 2 DESCRIPTION', 'closed');
        $this->repo->createIssue('TEST 3', 'TEST 3 DESCRIPTION');

        $issues = $this->repo->findByStatus('open');
        $this->assertCount(2, $issues);
        $this->assertEquals('TEST 1', $issues[0]['title']);
        $this->assertEquals('TEST 3', $issues[1]['title']);
    }
}