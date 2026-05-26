<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Controllers;

use Jacobk\PhpTier2\Services\LinkService;
use Jacobk\PhpTier2\Services\MetadataService;

class LinkController
{
    private LinkService $links;
    private MetadataService $meta;

    public function __construct()
    {
        $this->links = new LinkService(__DIR__ . '/../../data/links.json');
        $this->meta  = new MetadataService();
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
        $this->links->delete($id);

        echo json_encode(['success' => true]);
    }

    /**
     * GET /search?q=term
     * Return filtered list
     */
    public function search()
    {
        $term = $_GET['q'] ?? '';
        $results = $this->links->search($term);

        echo json_encode($results);
    }
}
