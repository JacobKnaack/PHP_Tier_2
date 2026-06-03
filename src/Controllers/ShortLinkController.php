<?php
declare(strict_types=1);

namespace JacobK\PhpTier2\Controllers;

use Jacobk\PhpTier2\Services\LinkService;
use Jacobk\PhpTier2\Services\ShortLinkService;

class ShortLinkController
{
    private ShortLinkService $shortLinkService;
    private LinkService $linkService;

    public function __construct()
    {
        $this->shortLinkService = new ShortLinkService(
            __DIR__ . '/../../data/shortlinks.json',
            __DIR__ . '/../../data/events.json'
        );
        $this->linkService = new LinkService(
            __DIR__ . '/../../data/links.json'
        );
    }

    /**
     * Create a shortlink for a saved link
     * GET /links/{id}/share
     */
    public function create(string $id): void
    {
        $link = $this->linkService->find($id);
        if(!$link) {
            http_response_code(404);
            echo json_encode(['error' => 'Link not found']);
            return;
        }
        $shortlink = $this->shortLinkService->findByLinkId($id);
        if (!$shortlink) {
            $shortlink = $this->shortLinkService->create($link['url'], $id);
        }

        include __DIR__ . '/../../views/share.php';
    }

    /**
     * Redirect shortlink visitors
     * GET /s/{code}
     */
    public function redirect(string $code): void
    {
        $shortlink = $this->shortLinkService->resolve($code);
        if ($shortlink) {
            $this->shortLinkService->logEvent($shortlink['id'], [
                'ip_hash' => hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown'),
                'referrer' => $_SERVER['HTTP_REFERER'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            ]);

            header("Location: " . $shortlink['target_url'], true, 302);
            exit;
        } else {
            http_response_code(404);
            echo "Short link not found";
        }
    }

    /**
     * Show analytics for a shortlink
     * GET /links/{id}/stats
     */
    public function stats(string $id): void
    {
        $link = $this->linkService->find($id);
        if (!$link) {
            http_response_code(404);
            echo json_encode(['error' => 'Link not found']);
            return;
        }

        $shortlink = $this->shortLinkService->findByLinkId($id);
        if (!$shortlink) {
            http_response_code(404);
            echo json_encode(['error' => 'Short link not found']);
            return;
        }

        $events = $this->shortLinkService->getEvents($shortlink['id']);
        include __DIR__ . '/../../views/stats.php';
    }
}
