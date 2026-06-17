<?php
declare(strict_types=1);

namespace Tests\Model;

use PHPUnit\Framework\TestCase;
use Jacobk\PhpTier2\Model\Model;
use PDO;
use PDOException;

class TestModel extends Model
{
    protected string $table = 'users';
}

class ModelTest extends TestCase
{
    private TestModel $model;
    private PDO $pdo;

    protected function setUp(): void
    {
        // Create shared PDO so we can run schema SQL directly
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create the model using the SAME PDO connection
        $this->model = new TestModel($this->pdo);

        // Create schema
        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                role TEXT
            );
        ");

        // Seed data
        $this->pdo->exec("INSERT INTO users (name, role) VALUES ('Alice', 'Admin')");
        $this->pdo->exec("INSERT INTO users (name, role) VALUES ('Bob', 'editor')");
    }

    public function testFindReturnsCorrectRow(): void
    {
        $row = $this->model->find(1);

        $this->assertNotNull($row);
        $this->assertEquals('Alice', $row['name']);
        $this->assertEquals('Admin', $row['role']);
    }

    public function testAllReturnsAllRows(): void
    {
        $rows = $this->model->all();

        $this->assertCount(2, $rows);
    }

    public function testCreateInsertsRow(): void
    {
        $id = $this->model->create([
            'name' => 'Charlie',
            'role' => 'viewer'
        ]);

        $row = $this->model->find($id);

        $this->assertEquals('Charlie', $row['name']);
        $this->assertEquals('viewer', $row['role']);
    }

    public function testUpdateModifiesRow(): void
    {
        $this->model->update(1, ['role' => 'SuperAdmin']);

        $row = $this->model->find(1);

        $this->assertEquals('SuperAdmin', $row['role']);
    }

    public function testDeleteRemovesRow(): void
    {
        $this->model->delete(1);

        $row = $this->model->find(1);

        $this->assertNull($row);
    }

    public function testWhereFiltersRows(): void
    {
        $rows = $this->model->where('role', 'Admin');

        $this->assertCount(1, $rows);
        $this->assertEquals('Alice', $rows[0]['name']);
    }

    public function testConstructorThrowsExceptionOnInvalidDsn(): void
    {
        $this->expectException(PDOException::class);

        new TestModel('INVALID', 'user', 'pass');
    }
}
