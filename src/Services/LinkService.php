<?php
declare(strict_types=1);

namespace Jacobk\PhpTier2\Services;

class LinkService
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;

        // Ensure file exists
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }
    }

    /**
     * Load all links from JSON
     */
    public function all(): array
    {
        $json = file_get_contents($this->file);
        return json_decode($json, true) ?? [];
    }

    /**
     * Save array of links back to JSON
     */
    private function save(array $links): void
    {
        file_put_contents($this->file, json_encode($links, JSON_PRETTY_PRINT));
    }

    /**
     * Add a new link
     */
    public function add(string $url, array $metadata): void
    {
        $links = $this->all();

        $links[] = [
            'id'         => uniqid('', true),
            'url'        => $url,
            'title'      => $metadata['title'] ?? $url,
            'favicon'    => $metadata['favicon'] ?? null,
            'domain'     => $metadata['domain'] ?? null,
            'created_at' => date('c'),
            'read'       => false,
            'tags'       => []
        ];

        $this->save($links);
    }

    /**
     * Mark a link as read
     */
    public function markRead(string $id): void
    {
        $links = $this->all();

        foreach ($links as &$link) {
            if ($link['id'] === $id) {
                $link['read'] = true;
                break;
            }
        }

        $this->save($links);
    }

    /**
     * Delete a link
     */
    public function delete(string $id): void
    {
        $links = $this->all();

        $links = array_filter($links, fn($link) => $link['id'] !== $id);

        $this->save(array_values($links));
    }

    /**
     * Search links by title, URL, or domain
     */
    public function search(string $term): array
    {
        $term = strtolower($term);
        $links = $this->all();

        return array_values(array_filter($links, function ($link) use ($term) {
            return str_contains(strtolower($link['title']), $term)
                || str_contains(strtolower($link['url']), $term)
                || str_contains(strtolower($link['domain']), $term);
        }));
    }
}
