<?php
declare(strict_types=1);

namespace JacobK\PhpTier2\Services;

use JacobK\PhpTier2\Repositories\ShortLinkEventRepositoryInterface;

class EventService
{
    private ShortLinkEventRepositoryInterface $repo;

    public function __construct(ShortLinkEventRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Log an analytics event for a shortlink.
     */
    public function log(string $shortlinkId, array $data): array
    {
        return $this->repo->log($shortlinkId, $data);
    }

    /**
     * Get all analytics events for a shortlink.
     */
    public function getByShortlink(string $shortlinkId): array
    {
        return $this->repo->getByShortlink($shortlinkId);
    }

    /**
     * Delete all analytics events for a shortlink.
     */
    public function deleteByShortlink(string $shortlinkId): void
    {
        $this->repo->deleteByShortlink($shortlinkId);
    }
}

