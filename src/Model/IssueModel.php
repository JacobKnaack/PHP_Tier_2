<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Model;

class IssueModel extends Model
{
    protected string $table = 'issues';

    public static function schema(): array
    {
        return [
            'id' => [
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ],
            'title' => [
                'type' => 'text',
                'nullable' => false,
            ],
            'description' => [
                'type' => 'text',
                'nullable' => false,
            ],
            'status' => [
                'type' => 'ENUM',
                'values' => ['open', 'closed'],
                'default' => 'open',
            ],
            'created_at' => [
                'type' => 'text',
                'default' => date('c'),
            ]
        ];
    }
}