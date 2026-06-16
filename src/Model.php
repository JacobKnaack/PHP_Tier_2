<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2;
use PDO;

class Model
{
    private PDO $pdo;

    public function __construct(string $dsn, string $user, string $pass)
    {
        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Turn errors into exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetches arrays by default
            ]);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function query(string $sql, array $values): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        $results = $stmt->fetchAll();
        return $results;
    }
}
