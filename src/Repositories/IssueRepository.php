<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Repositories;

use Jacobk\PhpTier2\Model\IssueModel;
use Jacobk\PhpTier2\Model\Schema;

class IssueRepository
{
    private IssueModel $model;
    private Schema $schema;

    public function __construct(IssueModel $model, Schema $schema)
    {
        $this->model = $model;
        $this->schema = $schema;
    }

    public function init(): void
    {
        $this->schema->createTable('issues', IssueModel::schema());
    }

    public function createIssue(string $title, string $description, string $status = 'open'): int
    {
        return $this->model->create([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'created_at' => date('c'),
        ]);
    }

    public function getIssue(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function updateIssue(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function deleteIssue(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function listIssues(): array
    {
        return $this->model->all();
    }

    public function findByStatus(string $status): array
    {
        return $this->model->where('status', $status);
    }   
}