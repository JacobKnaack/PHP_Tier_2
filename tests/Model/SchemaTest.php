<?php
declare(strict_types=1);

namespace Tests\Schema;

use Jacobk\PhpTier2\Model\Schema;
use PDO;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    private PDO $pdo;
    private Schema $schema;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->schema = new Schema($this->pdo);
    }

    public function testCreateTableSuccess(): void
    {


        $this->schema->createTable('users', [
            'id' => [
                'type'=>'integer',
                'primary' => true,
                'autoincrement' => true,
            ],
            'name' => [
                'type' => 'text',
                'nullable' => true,
            ],
            'role' => [
                'type' => 'text',
                'nullable' => false,
                'default' => 'user'
            ]
        ]);

        $stmt = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name='users'"
        );

        $this->assertEquals('users', $stmt->fetchColumn());
    }

    public function testBuildColumnSqlGeneratesSqlCorrectly(): void
    {
        $sql = $this->schema->buildColumnSql('id', [
            'type' => 'integer',
            'primary' => true,
            'autoIncrement' => true,
            'nullable' => false,
        ]);

        $this->assertEquals(
            'id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL',
            $sql
        );
    }

    public function testBuildColumnSqlHandlesDefaults(): void
    {
        $sql = $this->schema->buildColumnSql('role', [
            'type' => 'text',
            'default' => 'guest',
        ]);

        $this->assertEquals(
            "role TEXT DEFAULT 'guest'",
            $sql
        );
    }

    public function testBuildColumnSqlHandlesEnum(): void
    {
        $sql = $this->schema->buildColumnSql('status', [
            'type' => 'enum',
            'values' => ['open', 'closed'],
            'default' => 'open',
        ]);

        $this->assertEquals(
            "status TEXT CHECK(status IN ('open', 'closed')) DEFAULT 'open'",
            $sql
        );
    }
}