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

        $parts = ["$name $type"];

        if (($def['primary'] ?? false) === true) {
            $parts[] = "PRIMARY KEY";
        }

        if (($def['autoIncrement'] ?? false) === true) {
            // SQLite syntax; you can branch for MySQL/Postgres later
            $parts[] = "AUTOINCREMENT";
        }

        if (($def['nullable'] ?? true) === false) {
            $parts[] = "NOT NULL";
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
