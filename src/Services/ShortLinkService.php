<?php
declare(strict_types=1);

namespace JacobK\PhpTier2\Services;

use Jacobk\PhpTier2\Repositories\ShortLinkRepositoryInterface;
use Jacobk\PhpTier2\Repositories\ShortLinkEventRepositoryInterface;

class ShortLinkService
{
    private ShortLinkRepositoryInterface $shortlinks;
    private ShortLinkEventRepositoryInterface $events;

    public function __construct(
        ShortLinkRepositoryInterface $shortlinks,
        ShortLinkEventRepositoryInterface $events
    ) {
        $this->shortlinks = $shortlinks;
        $this->events = $events;
    }

    /**
     * Generate a short base62 code.
     */
    public function generateCode(int $length = 6): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $code;
    }

    /**
     * Create a shortlink for a URL + link ID.
     */
    public function create(string $url, string $linkId): array
    {
        // If a shortlink already exists for this link, return it
        $existing = $this->shortlinks->findByLinkId($linkId);
        if ($existing) {
            return $existing;
        }

        // Generate a unique code
        do {
            $code = $this->generateCode();
        } while ($this->shortlinks->findByCode($code) !== null);

        return $this->shortlinks->create($url, $linkId, $code);
    }

    /**
     * Resolve a short code to a shortlink record.
     */
    public function resolve(string $code): ?array
    {
        return $this->shortlinks->findByCode($code);
    }

    /**
     * Log an analytics event.
     */
    public function logEvent(string $shortlinkId, array $data): void
    {
        $this->events->log($shortlinkId, $data);
    }

    /**
     * Get analytics events for a shortlink.
     */
    public function getEvents(string $shortlinkId): array
    {
        return $this->events->getByShortlink($shortlinkId);
    }

    /**
     * Delete a shortlink and its events when the parent link is deleted.
     */
    public function deleteByLinkId(string $linkId): void
    {
        $sl = $this->shortlinks->findByLinkId($linkId);
        if ($sl) {
            $this->shortlinks->delete($sl['id']);
            $this->events->deleteByShortlink($sl['id']);
        }
    }
}

