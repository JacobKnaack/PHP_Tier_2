<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Model;
use PDO;

class Model
{
    private PDO $pdo;
    protected string $table;

    public function __construct(PDO|string $dsn, string $user = '', string $pass = '')
    {
        if ($dsn instanceof PDO) {
            $this->pdo = $dsn;
            return;
        }

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Turn errors into exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetches arrays by default
            ]);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function fetchOne(string $sql, array $params): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $fields = implode(', ', $columns);
        $placeholders = implode(', ', array_map(fn($c) => ":$c", $columns));

        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->execute($sql, $data);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $set = implode(', ', array_map(fn($c) => "$c = :$c", array_keys($data)));
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET $set WHERE id = :id";
        return $this->execute($sql, $data);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->fetchOne($sql, ['id' => $id]);
    }

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->fetchAll($sql);
    }


    public function where(string $column, mixed $value): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE $column = :value";
        return $this->fetchAll($sql, ['value' => $value]);
    }
}
