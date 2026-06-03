<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Controllers;

use Jacobk\PhpTier2\Services\ShortLinkService;
use Jacobk\PhpTier2\Services\LinkService;
use Jacobk\PhpTier2\Services\MetadataService;

class LinkController
{
    private LinkService $links;
    private MetadataService $meta;
    private ShortLinkService $shortLinkService;

    public function __construct()
    {
        $this->links = new LinkService(__DIR__ . '/../../data/links.json');
        $this->meta  = new MetadataService();
        $this->shortLinkService = new ShortLinkService(
            __DIR__ . '/../../data/shortlinks.json',
            __DIR__ . '/../../data/events.json'
        );
    }

    /**
     * GET /
     * Render the inbox page
     */
    public function index()
    {
        $links = $this->links->all();

        // Make $links available to the view
        include __DIR__ . '/../../views/inbox-list.php';
    }

    /**
     * POST /links
     * Add a new link
     */
    public function store()
    {
        $url = $_POST['url'] ?? '';

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid URL']);
            return;
        }

        // Fetch metadata (title, favicon, domain)
        $metadata = $this->meta->fetch($url);

        // Save link
        $this->links->add($url, $metadata);

        // Redirect back to inbox
        header('Location: /inbox-list');
        exit;
    }

    /**
     * POST /links/{id}/read
     * Mark a link as read
     */
    public function markRead(string $id)
    {
        $this->links->markRead($id);

        header('Location: /inbox-list');
        exit;
    }

    /**
     * DELETE /links/{id}
     * Delete a link
     */
    public function destroy(string $id)
    {
        $this->shortLinkService->deleteByLinkId($id);

        $this->links->delete($id);

        $returnTo = $_POST['return_to'] ?? '/inbox';
        header("Location: $returnTo");
        exit;
    }

    /**
     * GET /search?q=term
     * Return filtered list
     */
    public function search()
    {
        $term = $_GET['q'] ?? '';
        $results = $this->links->search($term);

        $links = $results; // Make results available to view

        include __DIR__ . '/../../views/search.php';
    }
}
