<?php
declare(strict_types=1);

namespace Tests\Model;

use PHPUnit\Framework\TestCase;
use Jacobk\PhpTier2\Model;
use PDOException;

class ModelTest extends TestCase
{
    private Model $model;

    protected function setUp(): void
    {
        $this->model = new Model('sqlite::memory:', '', '');

        $this->model->query(
            "CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, role TEXT)",
            [],
        );

        $this->model->query(
            "INSERT INTO users (name, role) VALUES (?, ?)",
            ['Alice', 'Admin'],
        );
        $this->model->query(
            "INSERT INTO users (name, role) VALUES (?, ?)",
            ['Bob', "editor"]
        );
    }

    public function testQueryReturnsCorrectData(): void
    {
        $sql = "SELECT name, role FROM users WHERE role = ?";
        $result = $this->model->query($sql, ['Admin']);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Alice', $result[0]['name']);
        $this->assertEquals('Admin', $result[0]['role']);
    }

    public function testQueryReturnsEmptyArrayWhenNoMatchFound(): void
    {
        $sql = "SELECT * FROM users WHERE role = ?";
        $results = $this->model->query($sql, ['SuperAdmin']);

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function testConstructorThrowsExceptionOnInvliadDsn(): void
    {
        $this->expectException(PDOException::class);

        new Model('INVALID', 'user', 'pass');
    }
}