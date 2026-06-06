<?php
declare(strict_types=1);
namespace Jacobk\PhpTier2\Repositories;

interface ShortLinkEventRepositoryInterface
{
    /**
     * Log an analytics event for a shortlink.
     */
    public function log(string $shortlinkId, array $data): array;

    /**
     * Get all analytics events for a shortlink.
     */
    public function getByShortlink(string $shortlinkId): array;

    /**
     * Delete all events for a shortlink.
     */
    public function deleteByShortlink(string $shortlinkId): void;
}
