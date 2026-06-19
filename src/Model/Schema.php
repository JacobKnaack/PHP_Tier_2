<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Model;
use PDO;

class Schema
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function driver(): string
    {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    private function buildEnum(string $name, array $values): string
    {
        $driver = $this->driver();
        if ($driver === 'sqlite') {
            $quoted = array_map(fn($v) => "'$v'", $values);
            return "TEXT CHECK($name IN (" . implode(', ', $quoted) . "))";
        }

        if ($driver === 'mysql') {
            $quoted = array_map(fn($v) => "'$v'", $values);
            return "ENUM(" . implode(', ', $quoted) . ")";
        }

        if ($driver === 'pgsql') {
            // Inline CHECK is simplest for now
            $quoted = array_map(fn($v) => "'$v'", $values);
            return "TEXT CHECK($name IN (" . implode(', ', $quoted) . "))";
        }

        throw new \RuntimeException("Unsupported driver: $driver");
    }

    private function buildAutoIncrement(): string
    {
        $driver = $this->driver();

        return match ($driver) {
            'sqlite' => 'AUTOINCREMENT',
            'mysql' => 'AUTO_INCREMENT',
            'pgsql' => 'GENERATED ALWAYS AS IDENTITY',
            default => throw new \RuntimeException("Unsupported driver: $driver"),
        };
    }

    public function buildColumnSql(string $name, array $def): string
    {
        $rawType = strtolower($def['type'] ?? 'text');

        // ENUM handling
        if ($rawType === 'enum') {
            $typeSql = $this->buildEnum($name, $def['values']);
            $parts = ["$name $typeSql"];   // FIX: prepend column name
        } else {
            $typeSql = strtoupper($rawType);
            $parts = ["$name $typeSql"];
        }

        // Primary key
        if (($def['primary'] ?? false) === true) {
            $parts[] = "PRIMARY KEY";
        }

        // Auto increment (support both autoIncrement and autoincrement)
        $auto = $def['autoIncrement'] ?? $def['autoincrement'] ?? false;
        if ($auto === true) {
            $parts[] = $this->buildAutoIncrement();
        }

        // Nullability
        if (($def['nullable'] ?? true) === false) {
            $parts[] = "NOT NULL";
        }

        // Default
        if (isset($def['default'])) {
            $default = $def['default'];
            $parts[] = "DEFAULT " . (is_string($default) ? "'$default'" : $default);
        }

        return implode(' ', $parts);
    }

    public function createTable(string $tableName, array $columns): void
    {
        $columnSql = [];

        foreach ($columns as $name => $definition) {
            $columnSql[] = $this->buildColumnSql($name, $definition);
        }

        $sql = sprintf(
            "CREATE TABLE IF NOT EXISTS %s (\n  %s\n);",
            $tableName,
            implode(",\n    ", $columnSql)
        );

        $this->pdo->exec($sql);
    }
}
