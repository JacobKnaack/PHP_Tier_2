<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Services;

use Jacobk\PhpTier2\Repositories\LinkRepositoryInterface;

class LinkService
{
    private LinkRepositoryInterface $repository;

    public function __construct(LinkRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Load all links from the repository
     */
    public function all(): array
    {
        return $this->repository->all();
    }

    /**
     * Add a new link
     */
    public function add(string $url, array $metadata): array
    {
        return $this->repository->add($url, $metadata);
    }

    /**
     * Mark a link as read
     */
    public function markRead(string $id): void
    {
        $this->repository->markRead($id);
    }

    /**
     * Delete a link
     */
    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * Search links by title, URL, or domain
     */
    public function search(string $term): array
    {
        return $this->repository->search($term);
    }

    /**
     * Find a link by ID
      * @return array|null Returns the link record or null if not found
     */
    public function find(string $id): ?array
    {
        return $this->repository->find($id);
    }
}
