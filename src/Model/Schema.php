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

    static function buildColumnSql(string $name, array $def): string
    {
        $type = strtoupper($def['type'] ?? 'TEXT');

        // ENUM support (SQLite emulation)
        if ($type === 'ENUM') {
            $type = 'TEXT'; // SQLite fallback
        }

        $parts = ["$name $type"];

        if (($def['primary'] ?? false) === true) {
            $parts[] = "PRIMARY KEY";
        }

        if (($def['autoIncrement'] ?? false) === true) {
            $parts[] = "AUTOINCREMENT"; // SQLITE specific
        }

        if (($def['nullable'] ?? true) === false) {
            $parts[] = "NOT NULL";
        }

        // ENUM CHECK constraint
        if (($def['type'] ?? '') === 'enum') {
            $values = $def['values'] ?? [];
            $quoted = array_map(fn($v) => "'$v'", $values);
            $parts[] = "CHECK($name IN (" . implode(', ', $quoted) . "))";
        }

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
            $columnSql[] = Schema::buildColumnSql($name, $definition);
        }

        $sql = sprintf(
            "CREATE TABLE IF NOT EXISTS %s (\n  %s\n);",
            $tableName,
            implode(",\n    ", $columnSql)
        );

        $this->pdo->exec($sql);
    }
}
