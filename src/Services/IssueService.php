<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Services;

use Jacobk\PhpTier2\Repositories\IssueRepository;

class IssueService
{
    private IssueRepository $repo;

    public function __construct(IssueRepository $repo)
    {
        $this->repo = $repo;
    }

    public function create(string $title, string $description, string $status = 'open'): array
    {
        $this->validateStatus($status);
        $this->validateTitle($title);

        $id = $this->repo->createIssue($title, $description, $status);

        return $this->repo->getIssue($id);
    }

    public function get(int $id): ?array
    {
        return $this->repo->getIssue($id);
    }

    public function update(int $id, array $data): ?array
    {
        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }

        if (isset($data['title'])) {
            $this->validateTitle($data['title']);
        }

        $this->repo->updateIssue($id, $data);

        return $this->repo->getIssue($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->deleteIssue($id);
    }

    public function list(): array
    {
        return $this->repo->listIssues();
    }

    public function findByStatus(string $status): array
    {
        $this->validateStatus($status);
        return $this->repo->findByStatus($status);
    }

    private function validateStatus(string $status): void
    {
        $allowed = ['open', 'closed'];

        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid status: $status");
        }
    }

    private function validateTitle(string $title): void
    {
        if (trim($title) === '') {
            throw new \InvalidArgumentException("Title cannot be empty");
        }
    }
}
